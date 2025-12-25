<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        
        // Register custom URL method for Azure storage
        Storage::disk('azure')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            return $this->getAzureUrl($path);
        });
    }
    
    /**
     * Generate a public URL for Azure Blob Storage
     */
    protected function getAzureUrl(string $path): string
    {
        $baseUrl = config('filesystems.disks.azure.url');
        $container = config('filesystems.disks.azure.container');
        
        // If URL already contains container, use it directly
        if (str_contains($baseUrl, $container)) {
            return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        }
        
        return rtrim($baseUrl, '/') . '/' . $container . '/' . ltrim($path, '/');
    }
}
