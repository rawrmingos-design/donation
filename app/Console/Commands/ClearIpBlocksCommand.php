<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearIpBlocksCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:clear-blocks {--ip= : Clear blocks for specific IP} {--all : Clear all IP blocks}';

    /**
     * The console command description.
     */
    protected $description = 'Clear IP blocks to resolve access denied errors';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔓 Clearing IP Blocks');
        $this->newLine();

        if ($this->option('all')) {
            return $this->clearAllBlocks();
        }

        if ($ip = $this->option('ip')) {
            return $this->clearIpBlocks($ip);
        }

        // Default: clear common development IPs
        return $this->clearDevelopmentBlocks();
    }

    /**
     * Clear all IP blocks
     */
    protected function clearAllBlocks(): int
    {
        $this->warn('⚠️ This will clear ALL IP blocks!');
        
        if (!$this->confirm('Are you sure you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Clear all IP-related cache entries
        $patterns = [
            'temp_block:*',
            'perm_block:*',
            'suspicious_requests:*',
            'suspicious_activity:*',
            'request_count:*',
        ];

        $cleared = 0;
        foreach ($patterns as $pattern) {
            // For Laravel cache, we need to clear by known keys
            // This is a simplified approach
            Cache::flush();
            $cleared++;
        }

        $this->info('✅ All IP blocks cleared successfully!');
        $this->line('All IPs can now access the application.');
        
        return 0;
    }

    /**
     * Clear blocks for specific IP
     */
    protected function clearIpBlocks(string $ip): int
    {
        $this->info("🎯 Clearing blocks for IP: {$ip}");

        $keys = [
            "temp_block:{$ip}",
            "perm_block:{$ip}",
            "suspicious_requests:{$ip}",
            "suspicious_activity:{$ip}",
            "request_count:{$ip}",
        ];

        $cleared = 0;
        foreach ($keys as $key) {
            if (Cache::forget($key)) {
                $cleared++;
                $this->line("  ✅ Cleared: {$key}");
            }
        }

        $this->info("✅ Cleared {$cleared} entries for IP {$ip}");
        return 0;
    }

    /**
     * Clear development IP blocks
     */
    protected function clearDevelopmentBlocks(): int
    {
        $this->info('🧹 Clearing development IP blocks...');

        $developmentIps = [
            '127.0.0.1',
            'localhost',
            '::1',
            '192.168.1.1',
        ];

        $totalCleared = 0;
        foreach ($developmentIps as $ip) {
            $keys = [
                "temp_block:{$ip}",
                "perm_block:{$ip}",
                "suspicious_requests:{$ip}",
                "suspicious_activity:{$ip}",
                "request_count:{$ip}",
            ];

            $cleared = 0;
            foreach ($keys as $key) {
                if (Cache::forget($key)) {
                    $cleared++;
                }
            }

            if ($cleared > 0) {
                $this->line("  ✅ Cleared {$cleared} entries for {$ip}");
                $totalCleared += $cleared;
            }
        }

        // Also clear general security counters
        $generalKeys = [
            'blocked_ips_count',
            'suspicious_requests_count',
            'security_events_24h',
        ];

        foreach ($generalKeys as $key) {
            Cache::forget($key);
            $this->line("  ✅ Cleared: {$key}");
        }

        $this->info("✅ Cleared {$totalCleared} IP block entries");
        $this->line('Development IPs should now have full access.');
        
        return 0;
    }
}
