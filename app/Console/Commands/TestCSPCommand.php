<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestCSPCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:csp {url=http://localhost:8000}';

    /**
     * The console command description.
     */
    protected $description = 'Test Content Security Policy headers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $url = $this->argument('url');
        
        $this->info("🔍 Testing CSP Headers for: {$url}");
        $this->newLine();

        try {
            $response = Http::get($url);
            
            if ($response->successful()) {
                $this->info("✅ Response Status: {$response->status()}");
                
                // Check CSP header
                $csp = $response->header('Content-Security-Policy');
                if ($csp) {
                    $this->info("🛡️ Content-Security-Policy Found:");
                    $this->line($csp);
                    $this->newLine();
                    
                    // Parse CSP directives
                    $directives = explode(';', $csp);
                    $this->info("📋 CSP Directives:");
                    foreach ($directives as $directive) {
                        $directive = trim($directive);
                        if ($directive) {
                            $this->line("  • {$directive}");
                        }
                    }
                } else {
                    $this->warn("⚠️ No Content-Security-Policy header found!");
                }
                
                $this->newLine();
                
                // Check other security headers
                $securityHeaders = [
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => ['DENY', 'SAMEORIGIN'],
                    'X-XSS-Protection' => '1; mode=block',
                    'Referrer-Policy' => 'strict-origin-when-cross-origin',
                    'Strict-Transport-Security' => null,
                ];
                
                $this->info("🔒 Other Security Headers:");
                foreach ($securityHeaders as $header => $expected) {
                    $value = $response->header($header);
                    if ($value) {
                        $this->info("  ✅ {$header}: {$value}");
                    } else {
                        $this->warn("  ❌ {$header}: Not found");
                    }
                }
                
            } else {
                $this->error("❌ Request failed with status: {$response->status()}");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error testing CSP: " . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info("🎉 CSP Test completed!");
        
        return 0;
    }
}
