<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\SiteConfigService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteConfigService::class, function () {
            return new SiteConfigService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Globally expose cached dynamic site settings to ALL Blade templates
        View::composer('*', function ($view) {
            /** @var SiteConfigService $siteConfig */
            $siteConfig = app(SiteConfigService::class);
            $tenantId = request()->attributes->get('tenant_id');

            $settings = [];
            try {
                $settings = $siteConfig->all($tenantId);
            } catch (\Throwable $e) {
                // Graceful fallback during early migration / setup phase
                $settings = [];
            }

            $view->with('site_settings', $settings);
        });
    }
}
