<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class SecureFileUploadMiddleware
{
    /**
     * Maximum file size in bytes (10MB)
     */
    protected int $maxFileSize = 10 * 1024 * 1024;

    /**
     * Allowed MIME types
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    /**
     * Allowed file extensions
     */
    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'
    ];

    /**
     * Dangerous file signatures (magic bytes)
     */
    protected array $dangerousSignatures = [
        'exe' => ['4D5A'],
        'php' => ['3C3F706870'],
        'jsp' => ['3C25'],
        'asp' => ['3C25'],
        'js' => ['2F2A', '2F2F'],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('file') || $request->hasFile('featured_image') || $request->hasFile('id_card')) {
            $files = $this->getAllUploadedFiles($request);
            
            foreach ($files as $file) {
                if (!$this->validateFile($file)) {
                    return response()->json([
                        'error' => 'File upload validation failed',
                        'message' => 'The uploaded file is not allowed or contains malicious content.'
                    ], 422);
                }
            }
        }

        return $next($request);
    }

    /**
     * Get all uploaded files from request
     */
    protected function getAllUploadedFiles(Request $request): array
    {
        $files = [];
        
        foreach ($request->allFiles() as $key => $file) {
            if (is_array($file)) {
                $files = array_merge($files, $file);
            } else {
                $files[] = $file;
            }
        }
        
        return array_filter($files, fn($file) => $file instanceof UploadedFile);
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): bool
    {
        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            return false;
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            return false;
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            return false;
        }

        // Check file signature (magic bytes)
        if (!$this->validateFileSignature($file)) {
            return false;
        }

        // Check for embedded PHP code in images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if ($this->containsPhpCode($file)) {
                return false;
            }
        }

        // Validate image files
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (!$this->validateImageFile($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate file signature (magic bytes)
     */
    protected function validateFileSignature(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return false;
        }

        $bytes = fread($handle, 10);
        fclose($handle);

        $hex = strtoupper(bin2hex($bytes));

        // Check for dangerous signatures
        foreach ($this->dangerousSignatures as $type => $signatures) {
            foreach ($signatures as $signature) {
                if (str_starts_with($hex, $signature)) {
                    return false;
                }
            }
        }

        // Validate expected signatures for images
        $extension = strtolower($file->getClientOriginalExtension());
        $expectedSignatures = [
            'jpg' => ['FFD8FF'],
            'jpeg' => ['FFD8FF'],
            'png' => ['89504E47'],
            'gif' => ['474946'],
            'webp' => ['52494646'],
            'pdf' => ['255044462D'],
        ];

        if (isset($expectedSignatures[$extension])) {
            foreach ($expectedSignatures[$extension] as $signature) {
                if (str_starts_with($hex, $signature)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Check if file contains PHP code
     */
    protected function containsPhpCode(UploadedFile $file): bool
    {
        $content = file_get_contents($file->getRealPath());
        
        $phpPatterns = [
            '/<\?php/',
            '/<\?=/',
            '/<\?/',
            '/<%/',
            '/eval\s*\(/',
            '/exec\s*\(/',
            '/system\s*\(/',
            '/shell_exec\s*\(/',
            '/passthru\s*\(/',
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate image file using GD library
     */
    protected function validateImageFile(UploadedFile $file): bool
    {
        try {
            $imageInfo = getimagesize($file->getRealPath());
            
            if ($imageInfo === false) {
                return false;
            }

            // Check if it's a valid image type
            $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
            
            return in_array($imageInfo[2], $allowedImageTypes);
        } catch (\Exception $e) {
            return false;
        }
    }
}
