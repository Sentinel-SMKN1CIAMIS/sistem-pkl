<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CronEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_503_when_cron_secret_token_is_not_configured(): void
    {
        config(['services.cron.token' => '']);

        $response = $this->getJson('/cron/run?token=anything');

        $response->assertStatus(503)
            ->assertJson(['status' => 'error']);
    }

    public function test_returns_403_when_token_is_missing_or_invalid(): void
    {
        config(['services.cron.token' => 'super-secret-token-123']);

        $this->getJson('/cron/run')->assertStatus(403);
        $this->getJson('/cron/run?token=wrong-token')->assertStatus(403);
    }

    public function test_runs_successfully_with_valid_query_token(): void
    {
        config(['services.cron.token' => 'super-secret-token-123']);
        Cache::forget('cron_last_prune_date');

        $response = $this->getJson('/cron/run?token=super-secret-token-123');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'pruned' => true,
            ])
            ->assertJsonStructure(['status', 'ran_at', 'reminders', 'pruned']);

        // Second call on the same day should skip model:prune
        $secondResponse = $this->getJson('/cron/run?token=super-secret-token-123');

        $secondResponse->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'pruned' => false,
            ]);
    }

    public function test_runs_successfully_with_valid_header_token(): void
    {
        config(['services.cron.token' => 'super-secret-token-123']);

        $response = $this->withHeader('X-Cron-Token', 'super-secret-token-123')
            ->getJson('/cron/run');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }
}
