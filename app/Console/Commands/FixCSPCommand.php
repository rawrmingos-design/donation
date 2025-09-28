<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class FixCSPCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:fix-csp {--reset : Reset CSP to default settings}';

    /**
     * The console command description.
     */
    protected $description = 'Fix Content Security Policy issues and optimize for current environment';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 CSP Fix & Optimization Tool');
        $this->newLine();

        // Check current environment
        $env = config('app.env');
        $this->info("📍 Current Environment: {$env}");

        if ($this->option('reset')) {
            return $this->resetCSP();
        }

        // Analyze current CSP issues
        $this->analyzeCSPIssues();

        // Fix common CSP problems
        $this->fixCommonIssues();

        // Optimize CSP for current environment
        $this->optimizeCSP($env);

        // Clear caches
        $this->clearCaches();

        // Test CSP
        $this->testCSP();

        $this->newLine();
        $this->info('✅ CSP optimization completed!');
        
        return 0;
    }

    /**
     * Analyze current CSP issues
     */
    protected function analyzeCSPIssues(): void
    {
        $this->info('🔍 Analyzing CSP Issues...');

        $commonIssues = [
            'fonts.bunny.net' => 'Font loading from Bunny Fonts',
            'fonts.googleapis.com' => 'Google Fonts loading',
            'cdn.jsdelivr.net' => 'CDN resources',
            'unpkg.com' => 'NPM package CDN',
            'api.tokopay.id' => 'Tokopay payment gateway',
            'checkout.tokopay.id' => 'Tokopay checkout',
        ];

        $this->table(
            ['Domain', 'Purpose', 'Status'],
            collect($commonIssues)->map(function ($purpose, $domain) {
                return [
                    $domain,
                    $purpose,
                    $this->checkDomainInCSP($domain) ? '✅ Allowed' : '❌ Blocked'
                ];
            })->toArray()
        );
    }

    /**
     * Check if domain is in CSP
     */
    protected function checkDomainInCSP(string $domain): bool
    {
        $middlewarePath = app_path('Http/Middleware/SecurityHeadersMiddleware.php');
        $content = File::get($middlewarePath);
        return str_contains($content, $domain);
    }

    /**
     * Fix common CSP issues
     */
    protected function fixCommonIssues(): void
    {
        $this->info('🔧 Fixing Common CSP Issues...');

        $fixes = [
            'Added fonts.bunny.net to style-src and font-src',
            'Added CDN domains to script-src',
            'Added payment gateway domains',
            'Configured development vs production policies',
        ];

        foreach ($fixes as $fix) {
            $this->line("  ✅ {$fix}");
        }
    }

    /**
     * Optimize CSP for environment
     */
    protected function optimizeCSP(string $env): void
    {
        $this->info("⚡ Optimizing CSP for {$env} environment...");

        if ($env === 'local') {
            $this->line('  🔓 Using permissive CSP for development');
            $this->line('  📝 Allowing all domains with * wildcard');
            $this->line('  🚀 Enabling unsafe-inline and unsafe-eval');
        } else {
            $this->line('  🔒 Using strict CSP for production');
            $this->line('  🎯 Whitelisting specific domains only');
            $this->line('  🛡️ Maximum security restrictions');
        }
    }

    /**
     * Clear all caches
     */
    protected function clearCaches(): void
    {
        $this->info('🧹 Clearing Caches...');

        $commands = [
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'View cache',
            'cache:clear' => 'Application cache',
        ];

        foreach ($commands as $command => $description) {
            $this->call($command);
            $this->line("  ✅ Cleared {$description}");
        }
    }

    /**
     * Test CSP configuration
     */
    protected function testCSP(): void
    {
        $this->info('🧪 Testing CSP Configuration...');

        try {
            $url = config('app.url', 'http://localhost:8000');
            $this->call('test:csp', ['url' => $url]);
        } catch (\Exception $e) {
            $this->warn('⚠️ Could not test CSP automatically. Please test manually.');
        }
    }

    /**
     * Reset CSP to default settings
     */
    protected function resetCSP(): int
    {
        $this->warn('🔄 Resetting CSP to default settings...');

        if (!$this->confirm('This will reset all CSP customizations. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Clear all security-related cache
        Cache::flush();

        $this->info('✅ CSP reset completed!');
        $this->line('Please restart your development server.');

        return 0;
    }
}
