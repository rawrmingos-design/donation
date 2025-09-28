<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ClearRateLimitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rate-limit:clear {--all : Clear all rate limits} {--ip= : Clear rate limits for specific IP} {--user= : Clear rate limits for specific user}';

    /**
     * The console command description.
     */
    protected $description = 'Clear rate limiting data to resolve "too many attempts" errors';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Clearing Rate Limits');
        $this->newLine();

        if ($this->option('all')) {
            return $this->clearAllRateLimits();
        }

        if ($ip = $this->option('ip')) {
            return $this->clearIpRateLimits($ip);
        }

        if ($userId = $this->option('user')) {
            return $this->clearUserRateLimits($userId);
        }

        // Default: clear common rate limits
        return $this->clearCommonRateLimits();
    }

    /**
     * Clear all rate limits
     */
    protected function clearAllRateLimits(): int
    {
        $this->warn('⚠️ This will clear ALL rate limiting data!');
        
        if (!$this->confirm('Are you sure you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Clear all cache (includes rate limits)
        Cache::flush();
        
        $this->info('✅ All rate limits cleared successfully!');
        $this->line('All users can now make requests without rate limit restrictions.');
        
        return 0;
    }

    /**
     * Clear rate limits for specific IP
     */
    protected function clearIpRateLimits(string $ip): int
    {
        $this->info("🎯 Clearing rate limits for IP: {$ip}");

        $keys = [
            "global:{$ip}",
            "api:{$ip}",
            "auth:{$ip}",
            "share:{$ip}",
        ];

        $cleared = 0;
        foreach ($keys as $key) {
            if (RateLimiter::clear($key)) {
                $cleared++;
                $this->line("  ✅ Cleared: {$key}");
            }
        }

        $this->info("✅ Cleared {$cleared} rate limit entries for IP {$ip}");
        return 0;
    }

    /**
     * Clear rate limits for specific user
     */
    protected function clearUserRateLimits(string $userId): int
    {
        $this->info("👤 Clearing rate limits for User ID: {$userId}");

        $keys = [
            "donation:{$userId}",
            "campaign:{$userId}",
            "upload:{$userId}",
        ];

        $cleared = 0;
        foreach ($keys as $key) {
            if (RateLimiter::clear($key)) {
                $cleared++;
                $this->line("  ✅ Cleared: {$key}");
            }
        }

        $this->info("✅ Cleared {$cleared} rate limit entries for User {$userId}");
        return 0;
    }

    /**
     * Clear common rate limits that cause issues
     */
    protected function clearCommonRateLimits(): int
    {
        $this->info('🧹 Clearing common rate limit issues...');

        // Get common IPs and clear their limits
        $commonPatterns = [
            'global:127.0.0.1',
            'global:localhost',
            'api:127.0.0.1',
            'auth:127.0.0.1',
        ];

        $cleared = 0;
        foreach ($commonPatterns as $pattern) {
            if (RateLimiter::clear($pattern)) {
                $cleared++;
                $this->line("  ✅ Cleared: {$pattern}");
            }
        }

        // Also clear any cached rate limit data
        $cacheKeys = [
            'rate_limit_hits_24h',
            'failed_logins_24h',
            'suspicious_requests_count',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
            $this->line("  ✅ Cleared cache: {$key}");
        }

        $this->info("✅ Cleared {$cleared} rate limit entries and cache data");
        $this->line('You should now be able to navigate freely without rate limit errors.');
        
        return 0;
    }
}
