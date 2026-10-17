<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteConfigService
{
    private const CACHE_PREFIX = 'site_settings:';
    private const CACHE_TTL_SECONDS = 86400; // 24 hours

    /**
     * Retrieve all active settings for a tenant or global system, keyed by setting_key.
     *
     * @return array<string, mixed>
     */
    public function all(?string $tenantId = null): array
    {
        $cacheKey = self::CACHE_PREFIX . ($tenantId ?? 'system_global');

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($tenantId): array {
            $query = SiteSetting::query();
            if ($tenantId !== null) {
                $query->where(function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
                });
            } else {
                $query->whereNull('tenant_id');
            }

            $settings = $query->get();
            $result = [];

            foreach ($settings as $setting) {
                $val = $setting->setting_value;
                if ($setting->data_type === 'JSON') {
                    $decoded = json_decode($val, true);
                    $val = json_last_error() === JSON_ERROR_NONE ? $decoded : $val;
                }
                $result[$setting->setting_key] = $val;
            }

            return $result;
        });
    }

    /**
     * Retrieve a specific setting string value with a fallback.
     */
    public function get(string $key, ?string $default = null, ?string $tenantId = null): string
    {
        $all = $this->all($tenantId);
        if (array_key_exists($key, $all) && $all[$key] !== null) {
            return (string) $all[$key];
        }

        return $default ?? '';
    }

    /**
     * Retrieve a JSON structured setting with a fallback array.
     *
     * @param array<mixed> $default
     * @return array<mixed>
     */
    public function getJson(string $key, array $default = [], ?string $tenantId = null): array
    {
        $all = $this->all($tenantId);
        if (isset($all[$key]) && is_array($all[$key])) {
            return $all[$key];
        }

        return $default;
    }

    /**
     * Set or update a site setting dynamically and invalidate cache.
     */
    public function set(string $key, string $value, string $dataType = 'STRING', ?string $tenantId = null, bool $isPublic = true): SiteSetting
    {
        $setting = SiteSetting::updateOrCreate(
            ['tenant_id' => $tenantId, 'setting_key' => $key],
            ['setting_value' => $value, 'data_type' => $dataType, 'is_public' => $isPublic]
        );

        $this->flushCache($tenantId);

        return $setting;
    }

    /**
     * Flush cache when settings change.
     */
    public function flushCache(?string $tenantId = null): void
    {
        Cache::forget(self::CACHE_PREFIX . 'system_global');
        if ($tenantId !== null) {
            Cache::forget(self::CACHE_PREFIX . $tenantId);
        }
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
                foreach (\App\Models\Tenant::all() as $t) {
                    Cache::forget(self::CACHE_PREFIX . $t->id);
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Flush all settings caches unconditionally.
     */
    public function flushAllCache(): void
    {
        $this->flushCache(null);
    }

    /**
     * Compute the uppercase acronym/abbreviation of the platform system brand name.
     */
    public function getSystemAbbreviation(?string $tenantId = null): string
    {
        $brand = $this->get('brand_name', config('app.name', 'AllThingsCambridge'), $tenantId);
        $words = preg_split('/[\s_-]+/', trim($brand));
        if (count($words) > 1) {
            $abbr = '';
            foreach ($words as $w) {
                if (!empty($w)) {
                    $abbr .= strtoupper(substr($w, 0, 1));
                }
            }
        } else {
            preg_match_all('/[A-Z]/', $brand, $matches);
            if (!empty($matches[0]) && count($matches[0]) >= 2) {
                $abbr = implode('', $matches[0]);
            } else {
                $clean = preg_replace('/[^a-zA-Z0-9]/', '', $brand);
                $abbr = strtoupper(substr($clean, 0, 3));
            }
        }

        return !empty($abbr) ? $abbr : 'ATC';
    }

    /**
     * Generate an accredited Faculty Staff ID prefixed with the system name abbreviation.
     */
    public function generateFacultyStaffId(?string $tenantId = null): string
    {
        $abbr = $this->getSystemAbbreviation($tenantId);
        return $abbr . '-FAC-' . str_pad((string) mt_rand(1001, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate an accredited Student / Scholar ID prefixed with the system name abbreviation.
     */
    public function generateStudentId(?string $tenantId = null): string
    {
        $abbr = $this->getSystemAbbreviation($tenantId);
        return $abbr . '-STU-' . date('Y') . '-' . str_pad((string) mt_rand(101, 999), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate an accredited Corporate Employee ID prefixed with the system name abbreviation.
     */
    public function generateCorporateId(?string $tenantId = null): string
    {
        $abbr = $this->getSystemAbbreviation($tenantId);
        return $abbr . '-EMP-' . str_pad((string) mt_rand(10001, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the appropriate ID based on user persona type.
     */
    public function generateIdForType(string $userType, ?string $tenantId = null): string
    {
        return match ($userType) {
            'HYBRID' => $this->generateFacultyStaffId($tenantId),
            'EMPLOYEE' => $this->generateCorporateId($tenantId),
            default => $this->generateStudentId($tenantId),
        };
    }

    /**
     * Retrieve the system brand logo as a Data URI (base64) or clean absolute URL for emails.
     */
    public function getLogoDataUri(?string $tenantId = null): ?string
    {
        $logoUrl = $this->get('brand_logo_url', null, $tenantId);

        // Candidate paths to check locally on filesystem (prioritizing the currently configured logo)
        $candidates = [];
        if (!empty($logoUrl)) {
            $parsedPath = parse_url($logoUrl, PHP_URL_PATH);
            if ($parsedPath) {
                $candidates[] = public_path(ltrim($parsedPath, '/\\'));
            }
        }
        $candidates[] = public_path('images/site_logo_email.png');
        $candidates[] = public_path('images/site_logo.png');

        foreach ($candidates as $filePath) {
            if ($filePath && file_exists($filePath) && is_readable($filePath)) {
                $mime = mime_content_type($filePath) ?: 'image/png';
                $content = @file_get_contents($filePath);
                if ($content !== false && strlen($content) > 0) {
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        }

        return !empty($logoUrl) ? $logoUrl : null;
    }
}
