<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get the URL for a file stored in any disk
     * Handles Azure Blob Storage which doesn't have native url() support
     */
    public static function url(?string $path, string $disk = null): ?string
    {
        if (empty($path)) {
            return null;
        }
        
        $disk = $disk ?? config('filesystems.default');
        
        // For Azure storage, construct URL manually
        if ($disk === 'azure') {
            return self::getAzureUrl($path);
        }
        
        // For other disks, use Laravel's built-in url() method
        try {
            return Storage::disk($disk)->url($path);
        } catch (\Exception $e) {
            // Fallback: try to construct URL manually
            return self::getAzureUrl($path);
        }
    }
    
    /**
     * Generate a public URL for Azure Blob Storage
     */
    protected static function getAzureUrl(string $path): string
    {
        $baseUrl = config('filesystems.disks.azure.url');
        $container = config('filesystems.disks.azure.container', 'storage');
        
        // If base URL is not set, use default Azure format
        if (empty($baseUrl)) {
            $accountName = config('filesystems.disks.azure.account-name');
            $baseUrl = "https://{$accountName}.blob.core.windows.net/{$container}";
        }
        
        // If URL already contains container, use it directly
        if (str_contains($baseUrl, '/' . $container)) {
            return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        }
        
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
