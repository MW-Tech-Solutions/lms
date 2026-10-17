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

                // Ensure brand_logo_url never points to localhost on a live server
                if (!empty($settings['brand_logo_url']) && (str_contains($settings['brand_logo_url'], 'http://localhost') || str_contains($settings['brand_logo_url'], 'http://127.0.0.1'))) {
                    $parsed = parse_url($settings['brand_logo_url'], PHP_URL_PATH);
                    if ($parsed) {
                        $settings['brand_logo_url'] = $parsed;
                    }
                }

                // If brand_logo_url is not set, use canonical local asset if present
                if (empty($settings['brand_logo_url'])) {
                    if (file_exists(public_path('images/site_logo.png'))) {
                        $settings['brand_logo_url'] = '/images/site_logo.png';
                    }
                }

                // Ensure brand_favicon_url never points to localhost on a live server
                if (!empty($settings['brand_favicon_url']) && (str_contains($settings['brand_favicon_url'], 'http://localhost') || str_contains($settings['brand_favicon_url'], 'http://127.0.0.1'))) {
                    $parsedFav = parse_url($settings['brand_favicon_url'], PHP_URL_PATH);
                    if ($parsedFav) {
                        $settings['brand_favicon_url'] = $parsedFav;
                    }
                }

                // If brand_favicon_url is not set, use canonical local favicon or logo asset
                if (empty($settings['brand_favicon_url'])) {
                    if (file_exists(public_path('favicon.ico')) && filesize(public_path('favicon.ico')) > 0) {
                        $settings['brand_favicon_url'] = '/favicon.ico';
                    } elseif (file_exists(public_path('uploads/branding/favicon_32x32.png'))) {
                        $settings['brand_favicon_url'] = '/uploads/branding/favicon_32x32.png';
                    } elseif (file_exists(public_path('images/site_logo.png'))) {
                        $settings['brand_favicon_url'] = '/images/site_logo.png';
                    }
                }

                // If brand_name is empty, fallback to system name
                if (empty($settings['brand_name'])) {
                    $settings['brand_name'] = config('app.name', 'AllThingsCambridge');
                }

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
