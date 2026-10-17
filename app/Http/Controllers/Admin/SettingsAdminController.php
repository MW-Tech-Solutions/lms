<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\SiteConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsAdminController extends Controller
{
    /**
     * 10 Professional Enterprise Color Templates.
     */
    public const COLOR_TEMPLATES = [
        'default' => [
            'key' => 'default',
            'name' => 'Edunex Royal Violet',
            'badge' => 'DEFAULT',
            'description' => 'Original Edunex flagship theme with rich deep navy and royal violet accents',
            'primary' => '#0F1321',
            'accent' => '#723EC3',
            'accent_hover' => '#5E32A6',
            'secondary' => '#F8F9FD',
            'accent_rgb' => '114, 62, 195',
        ],
        'emerald' => [
            'key' => 'emerald',
            'name' => 'Enterprise Emerald & Slate',
            'badge' => 'COMPLIANCE',
            'description' => 'Trust-focused forest green, slate borders, and clean institutional mint surfaces',
            'primary' => '#062319',
            'accent' => '#059669',
            'accent_hover' => '#047857',
            'secondary' => '#F0FDF4',
            'accent_rgb' => '5, 150, 105',
        ],
        'cobalt' => [
            'key' => 'cobalt',
            'name' => 'Executive Cyber Cobalt',
            'badge' => 'ENTERPRISE',
            'description' => 'Fintech-grade electric cobalt blue with high-contrast deep midnight surfaces',
            'primary' => '#0A1128',
            'accent' => '#2563EB',
            'accent_hover' => '#1D4ED8',
            'secondary' => '#EFF6FF',
            'accent_rgb' => '37, 99, 235',
        ],
        'crimson' => [
            'key' => 'crimson',
            'name' => 'Crimson Authority & Onyx',
            'badge' => 'GOVERNANCE',
            'description' => 'High-stakes executive ruby crimson with obsidian dark structural elements',
            'primary' => '#180C10',
            'accent' => '#DC2626',
            'accent_hover' => '#B91C1C',
            'secondary' => '#FEF2F2',
            'accent_rgb' => '220, 38, 38',
        ],
        'nordic' => [
            'key' => 'nordic',
            'name' => 'Nordic Cyan & Polar Indigo',
            'badge' => 'TECH & AI',
            'description' => 'Scandinavian arctic cyan and polar deep indigo for modern technical academies',
            'primary' => '#0F172A',
            'accent' => '#0EA5E9',
            'accent_hover' => '#0284C7',
            'secondary' => '#F0F9FF',
            'accent_rgb' => '14, 165, 233',
        ],
        'amber' => [
            'key' => 'amber',
            'name' => 'Luxury Amber Gold & Bronze',
            'badge' => 'ACADEMIC',
            'description' => 'Prestige university amber gold with warm dark chocolate obsidian backdrops',
            'primary' => '#1A1612',
            'accent' => '#D97706',
            'accent_hover' => '#B45309',
            'secondary' => '#FFFBEB',
            'accent_rgb' => '217, 119, 6',
        ],
        'amethyst' => [
            'key' => 'amethyst',
            'name' => 'Amethyst Magenta & Night',
            'badge' => 'CREATIVE',
            'description' => 'Futuristic royal magenta purple with deep interstellar gradient styling',
            'primary' => '#180828',
            'accent' => '#9333EA',
            'accent_hover' => '#7E22CE',
            'secondary' => '#FAF5FF',
            'accent_rgb' => '147, 51, 234',
        ],
        'teal' => [
            'key' => 'teal',
            'name' => 'Teal Sovereign & Deep Aqua',
            'badge' => 'HEALTHCARE',
            'description' => 'Clinical and bio-tech marine aqua with calm ocean deep foundations',
            'primary' => '#071B20',
            'accent' => '#0D9488',
            'accent_hover' => '#0F766E',
            'secondary' => '#F0FDFA',
            'accent_rgb' => '13, 148, 136',
        ],
        'sunset' => [
            'key' => 'sunset',
            'name' => 'Sunset Forge & Navy',
            'badge' => 'INDUSTRIAL',
            'description' => 'High-energy solar blaze orange paired with authoritative naval blue cards',
            'primary' => '#0D1B2A',
            'accent' => '#EA580C',
            'accent_hover' => '#C2410C',
            'secondary' => '#FFF7ED',
            'accent_rgb' => '234, 88, 12',
        ],
        'titanium' => [
            'key' => 'titanium',
            'name' => 'Minimalist Titanium Indigo',
            'badge' => 'MINIMALIST',
            'description' => 'Silicon Valley graphite neutral paired with refined ultraviolet indigo tokens',
            'primary' => '#18181B',
            'accent' => '#4F46E5',
            'accent_hover' => '#4338CA',
            'secondary' => '#EEF2FF',
            'accent_rgb' => '79, 70, 229',
        ],
    ];

    public function index(SiteConfigService $config): View
    {
        $templates = self::COLOR_TEMPLATES;
        $activePreset = $config->get('color_template_preset', 'default');

        return view('admin.settings.index', compact('templates', 'activePreset'));
    }

    public function update(Request $request, SiteConfigService $config): RedirectResponse
    {
        $request->validate([
            'logo_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'favicon_file' => ['nullable', 'file', 'mimes:ico,png,svg,jpg,jpeg', 'max:2048'],
        ]);

        $settings = $request->input('settings', []);

        // 1. Check for Preset Color Template selection
        $selectedPreset = $request->input('color_template_preset');
        if ($selectedPreset && isset(self::COLOR_TEMPLATES[$selectedPreset])) {
            $tpl = self::COLOR_TEMPLATES[$selectedPreset];
            $config->set('color_template_preset', $selectedPreset, 'STRING');
            $config->set('brand_primary_color', $tpl['primary'], 'COLOR');
            $config->set('brand_accent_color', $tpl['accent'], 'COLOR');
            $config->set('brand_accent_hover_color', $tpl['accent_hover'], 'COLOR');
            $config->set('brand_secondary_color', $tpl['secondary'], 'COLOR');
            $config->set('brand_accent_rgb', $tpl['accent_rgb'], 'STRING');
        }

        // 2. Handle System Logo File Upload
        if ($request->hasFile('logo_file')) {
            $logoDir = public_path('uploads/branding');
            if (!is_dir($logoDir)) {
                @mkdir($logoDir, 0755, true);
            }

            $file = $request->file('logo_file');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($logoDir, $filename);

            // Store clean root-relative path so it works across any domain, port, and HTTPS without mixed content
            $settings['brand_logo_url'] = '/uploads/branding/' . $filename;

            // Also copy to canonical public/images/site_logo.png and public/images/site_logo_email.png
            $imagesDir = public_path('images');
            if (!is_dir($imagesDir)) {
                @mkdir($imagesDir, 0755, true);
            }
            @copy($logoDir . '/' . $filename, $imagesDir . '/site_logo.png');
            @copy($logoDir . '/' . $filename, $imagesDir . '/site_logo_email.png');
        }

        // 3. Handle System Favicon File Upload
        if ($request->hasFile('favicon_file')) {
            $favDir = public_path('uploads/branding');
            if (!is_dir($favDir)) {
                @mkdir($favDir, 0755, true);
            }

            $fav = $request->file('favicon_file');
            $favExt = strtolower($fav->getClientOriginalExtension());
            $favFilename = 'favicon_' . time() . '.' . $favExt;
            $fullFavPath = $favDir . '/' . $favFilename;
            $fav->move($favDir, $favFilename);

            // If GD is available and file is an image (png, jpg, jpeg, webp), optimize/resize to lightweight favicon
            if (function_exists('imagecreatetruecolor') && in_array($favExt, ['png', 'jpg', 'jpeg', 'webp'])) {
                $srcImg = @imagecreatefromstring((string) file_get_contents($fullFavPath));
                if ($srcImg) {
                    $origW = imagesx($srcImg);
                    $origH = imagesy($srcImg);

                    // 48x48 high-DPI favicon
                    $fav48 = imagecreatetruecolor(48, 48);
                    imagealphablending($fav48, false);
                    imagesavealpha($fav48, true);
                    $trans48 = imagecolorallocatealpha($fav48, 255, 255, 255, 127);
                    imagefilledrectangle($fav48, 0, 0, 48, 48, $trans48);
                    imagecopyresampled($fav48, $srcImg, 0, 0, 0, 0, 48, 48, $origW, $origH);
                    imagepng($fav48, $fullFavPath, 9);

                    // 32x32 standard favicon
                    $fav32 = imagecreatetruecolor(32, 32);
                    imagealphablending($fav32, false);
                    imagesavealpha($fav32, true);
                    $trans32 = imagecolorallocatealpha($fav32, 255, 255, 255, 127);
                    imagefilledrectangle($fav32, 0, 0, 32, 32, $trans32);
                    imagecopyresampled($fav32, $srcImg, 0, 0, 0, 0, 32, 32, $origW, $origH);

                    // Generate valid Windows ICO file with 32x32 PNG payload
                    ob_start();
                    imagepng($fav32, null, 9);
                    $pngData = ob_get_clean();
                    $pngSize = strlen((string) $pngData);

                    $icoHeader = pack('vvv', 0, 1, 1);
                    $icoDirEntry = pack('CCCCvvVV', 32, 32, 0, 0, 1, 32, $pngSize, 22);
                    $icoContent = $icoHeader . $icoDirEntry . $pngData;

                    file_put_contents(public_path('favicon.ico'), $icoContent);
                }
            } else {
                @copy($fullFavPath, public_path('favicon.ico'));
            }

            $settings['brand_favicon_url'] = '/uploads/branding/' . $favFilename;
        }

        // 3b. Handle Hero Slides 1, 2, and 3 File Uploads
        $heroUploadDir = public_path('uploads/hero');
        if (!is_dir($heroUploadDir)) {
            @mkdir($heroUploadDir, 0755, true);
        }
        foreach ([1, 2, 3] as $slideNum) {
            $fileInputKey = "hero_slide_{$slideNum}_file";
            if ($request->hasFile($fileInputKey)) {
                $slideFile = $request->file($fileInputKey);
                $slideFileName = "hero_slide_{$slideNum}_" . time() . '.' . $slideFile->getClientOriginalExtension();
                $slideFile->move($heroUploadDir, $slideFileName);
                $settings["hero_slide_{$slideNum}_image"] = '/uploads/hero/' . $slideFileName;
            }
        }

        // 4. Update standard setting values across Global AND all Tenants
        $tenants = \Illuminate\Support\Facades\Schema::hasTable('tenants')
            ? \App\Models\Tenant::all()
            : collect();

        foreach ($settings as $key => $value) {
            if ($value !== null) {
                // If value points to localhost upload/image, normalize to root-relative
                if (is_string($value) && (str_contains($value, 'http://localhost') || str_contains($value, 'http://127.0.0.1'))) {
                    $parsed = parse_url($value, PHP_URL_PATH);
                    if ($parsed && (str_starts_with($parsed, '/uploads') || str_starts_with($parsed, '/images'))) {
                        $value = $parsed;
                    }
                }

                $dataType = str_ends_with((string) $key, '_color') ? 'COLOR' : 'STRING';

                // Update global setting (tenant_id = null)
                $config->set((string) $key, (string) $value, $dataType, null);

                // Update tenant-specific setting rows so they never silently override global values
                foreach ($tenants as $t) {
                    $config->set((string) $key, (string) $value, $dataType, $t->id);
                }

                // Also update any other legacy row matching this setting_key in site_settings table
                \App\Models\SiteSetting::where('setting_key', $key)->update([
                    'setting_value' => (string) $value,
                    'data_type' => $dataType,
                ]);
            }
        }

        // If brand_name or brand_logo_url was updated, sync Tenant models too
        if (!empty($settings['brand_name']) || !empty($settings['brand_logo_url'])) {
            foreach ($tenants as $t) {
                $bJson = $t->branding_json ?? [];
                if (!empty($settings['brand_name'])) {
                    $bJson['logo_text'] = (string) $settings['brand_name'];
                    $t->name = (string) $settings['brand_name'];
                }
                if (!empty($settings['brand_logo_url'])) {
                    $bJson['logo_url'] = (string) $settings['brand_logo_url'];
                }
                $t->branding_json = $bJson;
                $t->save();
            }
        }

        // Flush all settings and compiled view caches so updates display immediately system-wide
        $config->flushAllCache();

        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        // 5. Audit Log
        AuditService::log('SETTINGS_UPDATE', null, null, [
            'updated_by' => auth()->user()->name ?? 'System Admin',
            'preset' => $selectedPreset ?? 'custom',
        ]);

        return back()->with('success', 'Dynamic CMS settings, brand assets, and color template successfully updated and broadcast system-wide.');
    }
}
