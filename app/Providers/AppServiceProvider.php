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

        // Enforce Bootstrap 5 pagination globally for clean, accessible page links
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Globally expose cached dynamic site settings to ALL Blade templates
        View::composer('*', function ($view) {
            /** @var SiteConfigService $siteConfig */
            $siteConfig = app(SiteConfigService::class);
            $tenantId = request()->attributes->get('tenant_id');

            $settings = [];
            try {
                $settings = $siteConfig->all($tenantId);
                $settings['system_abbreviation'] = $siteConfig->getSystemAbbreviation($tenantId);
                $settings['generated_faculty_id'] = $siteConfig->generateFacultyStaffId($tenantId);
                $settings['generated_student_id'] = $siteConfig->generateStudentId($tenantId);
                $settings['generated_corporate_id'] = $siteConfig->generateCorporateId($tenantId);
                $settings['email_logo_data_uri'] = $siteConfig->getLogoDataUri($tenantId);
            } catch (\Throwable $e) {
                // Graceful fallback during early migration / setup phase
                $settings = [
                    'system_abbreviation' => 'ATC',
                    'generated_faculty_id' => 'ATC-FAC-' . mt_rand(1001, 9999),
                    'generated_student_id' => 'ATC-STU-' . date('Y') . '-' . mt_rand(101, 999),
                    'generated_corporate_id' => 'ATC-EMP-' . mt_rand(10001, 99999),
                    'email_logo_data_uri' => null,
                ];
            }

            $view->with('site_settings', $settings);
        });
    }
}
