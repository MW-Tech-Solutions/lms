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
        Cache::forget(self::CACHE_PREFIX . ($tenantId ?? 'system_global'));
    }
}
