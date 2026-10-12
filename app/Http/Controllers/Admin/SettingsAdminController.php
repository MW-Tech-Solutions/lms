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
                mkdir($logoDir, 0755, true);
            }

            $file = $request->file('logo_file');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($logoDir, $filename);

            $settings['brand_logo_url'] = asset('uploads/branding/' . $filename);
        }

        // 3. Handle System Favicon File Upload
        if ($request->hasFile('favicon_file')) {
            $favDir = public_path('uploads/branding');
            if (!is_dir($favDir)) {
                mkdir($favDir, 0755, true);
            }

            $fav = $request->file('favicon_file');
            $favFilename = 'favicon_' . time() . '.' . $fav->getClientOriginalExtension();
            $fav->move($favDir, $favFilename);

            $settings['brand_favicon_url'] = asset('uploads/branding/' . $favFilename);
        }

        // 4. Update standard setting values
        foreach ($settings as $key => $value) {
            if ($value !== null) {
                $dataType = str_ends_with((string) $key, '_color') ? 'COLOR' : 'STRING';
                $config->set((string) $key, (string) $value, $dataType);
            }
        }

        $config->flushCache();

        // 5. Audit Log
        AuditService::log('SETTINGS_UPDATE', null, null, [
            'updated_by' => auth()->user()->name ?? 'System Admin',
            'preset' => $selectedPreset ?? 'custom',
        ]);

        return back()->with('success', 'Dynamic CMS settings, brand assets, and color template successfully updated and broadcast system-wide.');
    }
}
