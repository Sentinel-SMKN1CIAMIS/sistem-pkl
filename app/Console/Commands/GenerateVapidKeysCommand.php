<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeysCommand extends Command
{
    protected $signature = 'webpush:vapid {--show : Display the keys instead of modifying .env} {--force : Overwrite existing VAPID keys in .env}';

    protected $description = 'Generate VAPID public and private keys for Web Push notifications';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\RuntimeException $e) {
            $keys = $this->createVapidKeysWithExplicitConfig();
        }

        if ($this->option('show')) {
            $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
            $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
            return self::SUCCESS;
        }

        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->error('.env file not found. Run with --show to print keys.');
            return self::FAILURE;
        }

        $envContent = file_get_contents($envPath);
        $hasPublic = preg_match('/^VAPID_PUBLIC_KEY=.+$/m', $envContent);
        $hasPrivate = preg_match('/^VAPID_PRIVATE_KEY=.+$/m', $envContent);

        if (($hasPublic || $hasPrivate) && !$this->option('force')) {
            $this->warn('VAPID keys already exist in .env. Use --force to overwrite.');
            return self::SUCCESS;
        }

        $envContent = $this->upsertEnvKey($envContent, 'VAPID_PUBLIC_KEY', $keys['publicKey']);
        $envContent = $this->upsertEnvKey($envContent, 'VAPID_PRIVATE_KEY', $keys['privateKey']);
        if (!preg_match('/^VAPID_SUBJECT=/m', $envContent)) {
            $envContent = $this->upsertEnvKey($envContent, 'VAPID_SUBJECT', 'mailto:admin@pkl.smkn1ciamis.id');
        }

        file_put_contents($envPath, $envContent);

        $this->info('VAPID keys generated and saved to .env successfully.');
        return self::SUCCESS;
    }

    protected function upsertEnvKey(string $envContent, string $key, string $value): string
    {
        $pattern = "/^{$key}=.*$/m";
        $line = "{$key}={$value}";

        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $line, $envContent);
        }

        return rtrim($envContent) . PHP_EOL . $line . PHP_EOL;
    }

    protected function createVapidKeysWithExplicitConfig(): array
    {
        $configArgs = [
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ];

        if (php_ini_loaded_file()) {
            $candidate = dirname(php_ini_loaded_file()) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
            if (file_exists($candidate)) {
                $configArgs['config'] = $candidate;
            }
        }

        $key = openssl_pkey_new($configArgs);
        if ($key === false) {
            throw new \RuntimeException('Unable to create VAPID EC key pair.');
        }

        $details = openssl_pkey_get_details($key);
        $ec = $details['ec'] ?? [];
        $x = str_pad($ec['x'] ?? '', 32, "\0", STR_PAD_LEFT);
        $y = str_pad($ec['y'] ?? '', 32, "\0", STR_PAD_LEFT);
        $d = str_pad($ec['d'] ?? '', 32, "\0", STR_PAD_LEFT);

        $encode = fn(string $bin) => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');

        return [
            'publicKey' => $encode("\x04" . $x . $y),
            'privateKey' => $encode($d),
        ];
    }
}
