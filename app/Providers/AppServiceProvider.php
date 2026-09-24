<?php

namespace App\Providers;

use App\Support\TcpdfFonts;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Must run before any TCPDF instance is created so SetFont('arialbd')
        // resolves definitions from resources/fonts instead of vendor.
        TcpdfFonts::registerPath();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(
            fn () => Route::has('admin.houses.index') ? route('admin.houses.index') : '/admin'
        );

        \Illuminate\Pagination\Paginator::useTailwind();

        $this->ensureStorageDirectories();

        if (str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

    protected function ensureStorageDirectories(): void
    {
        $dirs = [
            storage_path('app/public/reports'),
            storage_path('framework/sessions'),
            storage_path('framework/cache/data'),
            storage_path('logs'),
        ];

        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
        }
    }
}
