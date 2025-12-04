<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
<<<<<<< Updated upstream
        //
=======
        // Gunakan config(), jangan env()
        $appUrl = config('app.url');

        if ($appUrl) {
            // Pastikan tidak ada slash di akhir URL agar tidak double slash
            // Misal: .../CekFormatTA/login bukan .../CekFormatTA//login
            URL::forceRootUrl(rtrim($appUrl, '/'));
        }

        // Paksa HTTPS hanya untuk production (Railway)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
>>>>>>> Stashed changes
    }
}
