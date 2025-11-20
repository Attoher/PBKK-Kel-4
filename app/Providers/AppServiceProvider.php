<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gunakan config(), jangan env()
        $appUrl = config('app.url'); 
        
        if ($appUrl) {
            // Pastikan tidak ada slash di akhir URL agar tidak double slash
            // Misal: .../CekFormatTA/login bukan .../CekFormatTA//login
            URL::forceRootUrl(rtrim($appUrl, '/'));
        }

        // Paksa HTTPS karena Senopati pakai SSL
        if ($this->app->environment('production') || $this->app->environment('local')) {
             URL::forceScheme('https');
        }
    }
}
