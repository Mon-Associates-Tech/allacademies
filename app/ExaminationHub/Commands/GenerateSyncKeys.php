<?php

namespace App\ExaminationHub\Commands;

use Illuminate\Console\Command;

/**
 * Generate Ed25519 key pair for exam package signing.
 *
 * This command generates a key pair using libsodium and outputs the
 * base64-encoded keys for configuration in the .env file.
 *
 * Usage: php artisan sync:keys:generate
 */
class GenerateSyncKeys extends Command
{
    protected $signature = 'sync:keys:generate';
    protected $description = 'Generate Ed25519 key pair for exam package signing';

    public function handle(): int
    {
        // Check if libsodium is available
        if (!extension_loaded('sodium')) {
            $this->error('The libsodium PHP extension is required but not installed.');
            $this->newLine();
            $this->info('Install libsodium using one of these methods:');
            $this->info('  - Ubuntu/Debian: sudo apt-get install php-sodium');
            $this->info('  - CentOS/RHEL: sudo yum install php-sodium');
            $this->info('  - macOS (Homebrew): brew install libsodium && pecl install sodium');
            $this->newLine();

            return Command::FAILURE;
        }

        $this->info('Generating Ed25519 key pair...');
        $this->newLine();

        // Generate the key pair using native PHP sodium_* functions
        $keyPair = \sodium_crypto_sign_keypair();
        $publicKey = \sodium_crypto_sign_publickey($keyPair);
        $privateKey = \sodium_crypto_sign_secretkey($keyPair);

        // Base64 encode for storage
        $publicKeyBase64 = base64_encode($publicKey);
        $privateKeyBase64 = base64_encode($privateKey);

        $this->info('Key pair generated successfully!');
        $this->newLine();

        $this->error('IMPORTANT: Keep the private key secure. Do not share it with centers.');
        $this->newLine();

        $this->info('Add these values to your .env file:');
        $this->newLine();

        // Central server configuration
        $this->info('=== Central Server (.env) ===');
        $this->info("CENTRAL_PRIVATE_KEY={$privateKeyBase64}");
        $this->info("CENTRAL_PUBLIC_KEY={$publicKeyBase64}");
        $this->newLine();

        // Center server configuration
        $this->info('=== Center Servers (.env) ===');
        $this->info("CENTRAL_PUBLIC_KEY={$publicKeyBase64}");
        $this->newLine();

        $this->info('The private key is used ONLY on the central server to sign exam packages.');
        $this->info('The public key is used on ALL servers (central + centers) to verify signatures.');
        $this->newLine();

        // Verify the keys work
        $testMessage = 'package integrity test';
        $signature = \sodium_crypto_sign_detached($testMessage, $privateKey);
        $verified = \sodium_crypto_sign_verify_detached($signature, $testMessage, $publicKey);

        if ($verified) {
            $this->info('Verification test: PASSED ✓');
        } else {
            $this->error('Verification test: FAILED ✗');
            $this->error('The generated keys do not work correctly. Please regenerate.');

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('Next steps:');
        $this->info('1. Add the keys to your .env files as shown above');
        $this->info('2. On the central server: set both CENTRAL_PRIVATE_KEY and CENTRAL_PUBLIC_KEY');
        $this->info('3. On each center server: set only CENTRAL_PUBLIC_KEY');
        $this->info('4. Clear config cache: php artisan config:clear');
        $this->newLine();

        return Command::SUCCESS;
    }
}
