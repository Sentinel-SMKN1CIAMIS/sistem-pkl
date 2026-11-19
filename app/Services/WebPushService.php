<?php

namespace App\Services;

use App\Jobs\SendWebPushBatchJob;
use App\Models\PushSubscription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $client = null;

    public function __construct(?WebPush $client = null)
    {
        $this->client = $client;
    }

    public function isConfigured(): bool
    {
        return !empty(config('services.webpush.public_key'))
            && !empty(config('services.webpush.private_key'));
    }

    protected function getClient(): WebPush
    {
        if ($this->client) {
            return $this->client;
        }

        $auth = [
            'VAPID' => [
                'subject' => config('services.webpush.subject', 'mailto:admin@pkl.smkn1ciamis.id'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ];

        $webPush = new WebPush($auth, logger: Log::getLogger());
        $webPush->setReuseVAPIDHeaders(true);

        return $this->client = $webPush;
    }

    /**
     * Send push notification to a single user across all their subscribed devices.
     */
    public function sendToUser(int $userId, array $payload): array
    {
        return $this->sendToUsers([$userId], $payload);
    }

    /**
     * Send push notifications to multiple users synchronously in 100-device chunks.
     */
    public function sendToUsers(array|Collection $userIds, array $payload): array
    {
        $ids = collect($userIds)->filter()->unique()->values();
        if ($ids->isEmpty() || (!$this->client && !$this->isConfigured())) {
            return ['sent' => 0, 'failed' => 0, 'expired' => 0];
        }

        $totals = ['sent' => 0, 'failed' => 0, 'expired' => 0];

        PushSubscription::whereIn('user_id', $ids->all())
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) use ($payload, &$totals) {
                $res = $this->sendToSubscriptions($subscriptions, $payload);
                $totals['sent'] += $res['sent'];
                $totals['failed'] += $res['failed'];
                $totals['expired'] += $res['expired'];
            });

        return $totals;
    }

    /**
     * Dispatch queued jobs in chunks of 100 users for large broadcasts.
     */
    public function dispatchBroadcast(array|Collection $userIds, array $payload): int
    {
        $ids = collect($userIds)->filter()->unique()->values();
        if ($ids->isEmpty() || !$this->isConfigured()) {
            return 0;
        }

        $chunks = $ids->chunk(100);
        foreach ($chunks as $chunk) {
            SendWebPushBatchJob::dispatch($chunk->values()->all(), $payload);
        }

        return $chunks->count();
    }

    /**
     * Send push notifications to a collection of PushSubscription models.
     */
    public function sendToSubscriptions(Collection $subscriptions, array $payload): array
    {
        $stats = ['sent' => 0, 'failed' => 0, 'expired' => 0];
        if ($subscriptions->isEmpty() || (!$this->client && !$this->isConfigured())) {
            return $stats;
        }

        $webPush = $this->getClient();
        $jsonPayload = json_encode([
            'title' => $payload['title'] ?? 'MAS-PKL Notifikasi',
            'body' => $payload['body'] ?? '',
            'icon' => $payload['icon'] ?? '/logo.png',
            'url' => $payload['url'] ?? '/notifikasi',
            'tag' => $payload['tag'] ?? 'mas-pkl-notif',
        ]);

        $endpointMap = [];
        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
                ]);
                $webPush->queueNotification($subscription, $jsonPayload);
                $endpointMap[$sub->endpoint] = $sub->id;
            } catch (\Throwable $e) {
                $stats['failed']++;
                Log::warning('WebPush invalid subscription skipped: ' . $e->getMessage(), [
                    'subscription_id' => $sub->id,
                ]);
            }
        }

        $expiredIds = [];
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if ($report->isSuccess()) {
                $stats['sent']++;
            } elseif ($report->isSubscriptionExpired()) {
                $stats['expired']++;
                if (isset($endpointMap[$endpoint])) {
                    $expiredIds[] = $endpointMap[$endpoint];
                }
            } else {
                $stats['failed']++;
                Log::warning('WebPush delivery failed: ' . $report->getReason(), [
                    'endpoint' => substr($endpoint, 0, 120),
                ]);
            }
        }

        if (!empty($expiredIds)) {
            PushSubscription::whereIn('id', $expiredIds)->delete();
        }

        return $stats;
    }
}
