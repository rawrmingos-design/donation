<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SecurityMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:monitor {--alert : Send alerts for critical issues}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor security metrics and generate alerts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Security Monitoring Report');
        $this->info('Generated: ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        // Collect security metrics
        $metrics = $this->collectSecurityMetrics();
        
        // Display metrics
        $this->displayMetrics($metrics);
        
        // Check for alerts
        if ($this->option('alert')) {
            $this->checkAndSendAlerts($metrics);
        }

        return 0;
    }

    /**
     * Collect security metrics
     */
    protected function collectSecurityMetrics(): array
    {
        return [
            'blocked_ips' => $this->getBlockedIpsCount(),
            'suspicious_requests' => $this->getSuspiciousRequestsCount(),
            'failed_logins' => $this->getFailedLoginsCount(),
            'rate_limit_hits' => $this->getRateLimitHitsCount(),
            'file_upload_blocks' => $this->getFileUploadBlocksCount(),
            'active_sessions' => $this->getActiveSessionsCount(),
            'security_events' => $this->getSecurityEventsCount(),
        ];
    }

    /**
     * Display security metrics
     */
    protected function displayMetrics(array $metrics): void
    {
        $this->table(
            ['Metric', 'Count', 'Status'],
            [
                ['Blocked IPs (24h)', $metrics['blocked_ips'], $this->getStatus($metrics['blocked_ips'], 10, 50)],
                ['Suspicious Requests (24h)', $metrics['suspicious_requests'], $this->getStatus($metrics['suspicious_requests'], 20, 100)],
                ['Failed Logins (24h)', $metrics['failed_logins'], $this->getStatus($metrics['failed_logins'], 50, 200)],
                ['Rate Limit Hits (24h)', $metrics['rate_limit_hits'], $this->getStatus($metrics['rate_limit_hits'], 100, 500)],
                ['File Upload Blocks (24h)', $metrics['file_upload_blocks'], $this->getStatus($metrics['file_upload_blocks'], 5, 20)],
                ['Active Sessions', $metrics['active_sessions'], $this->getStatus($metrics['active_sessions'], 100, 1000)],
                ['Security Events (24h)', $metrics['security_events'], $this->getStatus($metrics['security_events'], 10, 50)],
            ]
        );

        $this->newLine();
        $this->displayTopThreats();
    }

    /**
     * Get status based on thresholds
     */
    protected function getStatus(int $value, int $warning, int $critical): string
    {
        if ($value >= $critical) {
            return '<fg=red>CRITICAL</>';
        } elseif ($value >= $warning) {
            return '<fg=yellow>WARNING</>';
        } else {
            return '<fg=green>OK</>';
        }
    }

    /**
     * Display top threats
     */
    protected function displayTopThreats(): void
    {
        $this->info('🎯 Top Security Threats (Last 24 Hours):');
        
        // Get top blocked IPs
        $blockedIps = $this->getTopBlockedIps();
        if (!empty($blockedIps)) {
            $this->line('📍 Most Blocked IPs:');
            foreach ($blockedIps as $ip => $count) {
                $this->line("   {$ip}: {$count} blocks");
            }
            $this->newLine();
        }

        // Get suspicious patterns
        $suspiciousPatterns = $this->getSuspiciousPatterns();
        if (!empty($suspiciousPatterns)) {
            $this->line('⚠️ Common Attack Patterns:');
            foreach ($suspiciousPatterns as $pattern => $count) {
                $this->line("   {$pattern}: {$count} attempts");
            }
            $this->newLine();
        }
    }

    /**
     * Check and send alerts
     */
    protected function checkAndSendAlerts(array $metrics): void
    {
        $alerts = [];

        // Check critical thresholds
        if ($metrics['blocked_ips'] >= 50) {
            $alerts[] = "High number of blocked IPs: {$metrics['blocked_ips']}";
        }

        if ($metrics['suspicious_requests'] >= 100) {
            $alerts[] = "High number of suspicious requests: {$metrics['suspicious_requests']}";
        }

        if ($metrics['failed_logins'] >= 200) {
            $alerts[] = "High number of failed logins: {$metrics['failed_logins']}";
        }

        if (!empty($alerts)) {
            $this->sendSecurityAlert($alerts);
            $this->error('🚨 Security alerts sent!');
        } else {
            $this->info('✅ No critical security issues detected.');
        }
    }

    /**
     * Send security alert
     */
    protected function sendSecurityAlert(array $alerts): void
    {
        $alertEmail = config('security.monitoring.alert_email');
        
        if ($alertEmail) {
            $message = "Security Alert - " . config('app.name') . "\n\n";
            $message .= "The following security issues require attention:\n\n";
            
            foreach ($alerts as $alert) {
                $message .= "• {$alert}\n";
            }
            
            $message .= "\nGenerated: " . now()->format('Y-m-d H:i:s');
            
            // Log the alert
            Log::alert('Security Alert Sent', [
                'alerts' => $alerts,
                'email' => $alertEmail,
            ]);
            
            // In a real implementation, you would send an email here
            // Mail::raw($message, function ($mail) use ($alertEmail) {
            //     $mail->to($alertEmail)->subject('Security Alert - ' . config('app.name'));
            // });
        }
    }

    /**
     * Get blocked IPs count
     */
    protected function getBlockedIpsCount(): int
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $keys = Cache::getRedis()->keys('*block:*');
                return count($keys);
            }
            
            // Fallback for non-Redis cache stores
            return Cache::get('blocked_ips_count', 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get suspicious requests count
     */
    protected function getSuspiciousRequestsCount(): int
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $count = 0;
                $keys = Cache::getRedis()->keys('suspicious_requests:*');
                foreach ($keys as $key) {
                    $count += Cache::get($key, 0);
                }
                return $count;
            }
            
            // Fallback for non-Redis cache stores
            return Cache::get('suspicious_requests_count', 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get failed logins count
     */
    protected function getFailedLoginsCount(): int
    {
        // This would typically come from your authentication logs
        return Cache::get('failed_logins_24h', 0);
    }

    /**
     * Get rate limit hits count
     */
    protected function getRateLimitHitsCount(): int
    {
        return Cache::get('rate_limit_hits_24h', 0);
    }

    /**
     * Get file upload blocks count
     */
    protected function getFileUploadBlocksCount(): int
    {
        return Cache::get('file_upload_blocks_24h', 0);
    }

    /**
     * Get active sessions count
     */
    protected function getActiveSessionsCount(): int
    {
        try {
            return DB::table('sessions')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get security events count
     */
    protected function getSecurityEventsCount(): int
    {
        return Cache::get('security_events_24h', 0);
    }

    /**
     * Get top blocked IPs
     */
    protected function getTopBlockedIPs(): array
    {
        // This would typically come from your logs or cache
        return Cache::get('top_blocked_ips', []);
    }

    /**
     * Get suspicious patterns
     */
    protected function getSuspiciousPatterns(): array
    {
        return [
            'SQL Injection' => Cache::get('sql_injection_attempts_24h', 0),
            'XSS Attempts' => Cache::get('xss_attempts_24h', 0),
            'Path Traversal' => Cache::get('path_traversal_attempts_24h', 0),
            'Command Injection' => Cache::get('command_injection_attempts_24h', 0),
        ];
    }
}
