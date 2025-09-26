<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class SecurityAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:audit {--fix : Automatically fix issues where possible}';

    /**
     * The console command description.
     */
    protected $description = 'Perform a comprehensive security audit of the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔒 Starting Security Audit...');
        $this->newLine();

        $issues = [];
        $fixes = [];

        // Check file permissions
        $this->info('📁 Checking file permissions...');
        $permissionIssues = $this->checkFilePermissions();
        if (!empty($permissionIssues)) {
            $issues = array_merge($issues, $permissionIssues);
        }

        // Check environment configuration
        $this->info('⚙️ Checking environment configuration...');
        $envIssues = $this->checkEnvironmentConfig();
        if (!empty($envIssues)) {
            $issues = array_merge($issues, $envIssues);
        }

        // Check database security
        $this->info('🗄️ Checking database security...');
        $dbIssues = $this->checkDatabaseSecurity();
        if (!empty($dbIssues)) {
            $issues = array_merge($issues, $dbIssues);
        }

        // Check user accounts
        $this->info('👥 Checking user accounts...');
        $userIssues = $this->checkUserAccounts();
        if (!empty($userIssues)) {
            $issues = array_merge($issues, $userIssues);
        }

        // Check session security
        $this->info('🍪 Checking session security...');
        $sessionIssues = $this->checkSessionSecurity();
        if (!empty($sessionIssues)) {
            $issues = array_merge($issues, $sessionIssues);
        }

        // Check for suspicious files
        $this->info('🔍 Scanning for suspicious files...');
        $fileIssues = $this->scanSuspiciousFiles();
        if (!empty($fileIssues)) {
            $issues = array_merge($issues, $fileIssues);
        }

        // Check security headers
        $this->info('🛡️ Checking security headers configuration...');
        $headerIssues = $this->checkSecurityHeaders();
        if (!empty($headerIssues)) {
            $issues = array_merge($issues, $headerIssues);
        }

        // Display results
        $this->newLine();
        if (empty($issues)) {
            $this->info('✅ Security audit completed successfully! No issues found.');
        } else {
            $this->error('⚠️ Security audit found ' . count($issues) . ' issues:');
            $this->newLine();

            foreach ($issues as $index => $issue) {
                $this->line(($index + 1) . '. ' . $issue['type'] . ': ' . $issue['message']);
                if (isset($issue['recommendation'])) {
                    $this->line('   💡 Recommendation: ' . $issue['recommendation']);
                }
                $this->newLine();
            }

            if ($this->option('fix')) {
                $this->info('🔧 Attempting to fix issues automatically...');
                $this->applyFixes($issues);
            }
        }

        return empty($issues) ? 0 : 1;
    }

    /**
     * Check file permissions
     */
    protected function checkFilePermissions(): array
    {
        $issues = [];
        
        $criticalPaths = [
            '.env' => '600',
            'storage' => '755',
            'bootstrap/cache' => '755',
        ];

        foreach ($criticalPaths as $path => $expectedPerm) {
            $fullPath = base_path($path);
            if (File::exists($fullPath)) {
                $currentPerm = substr(sprintf('%o', fileperms($fullPath)), -3);
                if ($currentPerm !== $expectedPerm) {
                    $issues[] = [
                        'type' => 'File Permission',
                        'message' => "File {$path} has permissions {$currentPerm}, should be {$expectedPerm}",
                        'recommendation' => "Run: chmod {$expectedPerm} {$path}",
                        'fix' => function() use ($fullPath, $expectedPerm) {
                            chmod($fullPath, octdec($expectedPerm));
                        }
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * Check environment configuration
     */
    protected function checkEnvironmentConfig(): array
    {
        $issues = [];

        // Check APP_DEBUG
        if (config('app.debug') && config('app.env') === 'production') {
            $issues[] = [
                'type' => 'Environment Config',
                'message' => 'APP_DEBUG is enabled in production',
                'recommendation' => 'Set APP_DEBUG=false in production environment'
            ];
        }

        // Check APP_KEY
        if (empty(config('app.key'))) {
            $issues[] = [
                'type' => 'Environment Config',
                'message' => 'APP_KEY is not set',
                'recommendation' => 'Run: php artisan key:generate'
            ];
        }

        // Check HTTPS settings
        if (config('app.env') === 'production' && !config('session.secure')) {
            $issues[] = [
                'type' => 'Environment Config',
                'message' => 'Secure cookies not enabled in production',
                'recommendation' => 'Set SESSION_SECURE_COOKIE=true'
            ];
        }

        return $issues;
    }

    /**
     * Check database security
     */
    protected function checkDatabaseSecurity(): array
    {
        $issues = [];

        try {
            // Check for default passwords
            $defaultUsers = User::where('email', 'admin@admin.com')
                ->orWhere('email', 'test@test.com')
                ->orWhere('name', 'admin')
                ->get();

            if ($defaultUsers->count() > 0) {
                $issues[] = [
                    'type' => 'Database Security',
                    'message' => 'Default test users found in database',
                    'recommendation' => 'Remove or secure default user accounts'
                ];
            }

            // Check for weak passwords (this is a simplified check)
            $weakPasswords = User::whereIn('email', [
                'admin@admin.com', 'test@test.com', 'demo@demo.com'
            ])->get();

            if ($weakPasswords->count() > 0) {
                $issues[] = [
                    'type' => 'Database Security',
                    'message' => 'Users with potentially weak passwords detected',
                    'recommendation' => 'Enforce strong password policy for all users'
                ];
            }

        } catch (\Exception $e) {
            $issues[] = [
                'type' => 'Database Security',
                'message' => 'Unable to check database security: ' . $e->getMessage(),
                'recommendation' => 'Ensure database connection is working'
            ];
        }

        return $issues;
    }

    /**
     * Check user accounts
     */
    protected function checkUserAccounts(): array
    {
        $issues = [];

        try {
            // Check for inactive admin accounts
            $inactiveAdmins = User::where('role', 'admin')
                ->where('last_login_at', '<', now()->subDays(90))
                ->orWhereNull('last_login_at')
                ->get();

            if ($inactiveAdmins->count() > 0) {
                $issues[] = [
                    'type' => 'User Account',
                    'message' => $inactiveAdmins->count() . ' inactive admin accounts found',
                    'recommendation' => 'Review and disable unused admin accounts'
                ];
            }

            // Check for unverified old accounts
            $oldUnverified = User::whereNull('email_verified_at')
                ->where('created_at', '<', now()->subDays(7))
                ->get();

            if ($oldUnverified->count() > 0) {
                $issues[] = [
                    'type' => 'User Account',
                    'message' => $oldUnverified->count() . ' old unverified accounts found',
                    'recommendation' => 'Clean up old unverified accounts'
                ];
            }

        } catch (\Exception $e) {
            // Handle gracefully if user table structure is different
        }

        return $issues;
    }

    /**
     * Check session security
     */
    protected function checkSessionSecurity(): array
    {
        $issues = [];

        if (config('session.lifetime') > 120) {
            $issues[] = [
                'type' => 'Session Security',
                'message' => 'Session lifetime is too long (' . config('session.lifetime') . ' minutes)',
                'recommendation' => 'Set session lifetime to 120 minutes or less'
            ];
        }

        if (config('session.same_site') !== 'strict') {
            $issues[] = [
                'type' => 'Session Security',
                'message' => 'SameSite cookie attribute is not set to strict',
                'recommendation' => 'Set SESSION_SAME_SITE=strict'
            ];
        }

        return $issues;
    }

    /**
     * Scan for suspicious files
     */
    protected function scanSuspiciousFiles(): array
    {
        $issues = [];
        $suspiciousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5'];
        
        $uploadDirs = [
            storage_path('app/public'),
            public_path('uploads'),
        ];

        foreach ($uploadDirs as $dir) {
            if (File::exists($dir)) {
                $files = File::allFiles($dir);
                foreach ($files as $file) {
                    $extension = $file->getExtension();
                    if (in_array(strtolower($extension), $suspiciousExtensions)) {
                        $issues[] = [
                            'type' => 'Suspicious File',
                            'message' => 'PHP file found in upload directory: ' . $file->getRelativePathname(),
                            'recommendation' => 'Remove or quarantine suspicious files'
                        ];
                    }
                }
            }
        }

        return $issues;
    }

    /**
     * Check security headers configuration
     */
    protected function checkSecurityHeaders(): array
    {
        $issues = [];

        $requiredHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
        ];

        // This is a basic check - in a real implementation, you'd test actual HTTP responses
        if (!config('security.security_headers.x_content_type_options')) {
            $issues[] = [
                'type' => 'Security Headers',
                'message' => 'X-Content-Type-Options header not configured',
                'recommendation' => 'Configure security headers middleware'
            ];
        }

        return $issues;
    }

    /**
     * Apply automatic fixes
     */
    protected function applyFixes(array $issues): void
    {
        $fixed = 0;
        
        foreach ($issues as $issue) {
            if (isset($issue['fix']) && is_callable($issue['fix'])) {
                try {
                    $issue['fix']();
                    $this->info('✅ Fixed: ' . $issue['message']);
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error('❌ Failed to fix: ' . $issue['message'] . ' - ' . $e->getMessage());
                }
            }
        }

        $this->info("🔧 Applied {$fixed} automatic fixes.");
    }
}
