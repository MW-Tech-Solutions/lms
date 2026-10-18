<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SiteSetting;
use App\Models\Tenant;

return new class extends Migration
{
    public function up(): void
    {
        $stripeKey = env('STRIPE_KEY') ?: config('services.stripe.key');
        $stripeSecret = env('STRIPE_SECRET') ?: config('services.stripe.secret');

        if (empty($stripeKey) || empty($stripeSecret)) {
            return;
        }

        // 1. System global rows
        SiteSetting::updateOrCreate(
            ['tenant_id' => null, 'setting_key' => 'stripe_key'],
            ['setting_value' => $stripeKey, 'data_type' => 'STRING', 'is_public' => true]
        );
        SiteSetting::updateOrCreate(
            ['tenant_id' => null, 'setting_key' => 'stripe_secret'],
            ['setting_value' => $stripeSecret, 'data_type' => 'STRING', 'is_public' => false]
        );

        // 2. Tenant rows
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            SiteSetting::updateOrCreate(
                ['tenant_id' => $tenant->id, 'setting_key' => 'stripe_key'],
                ['setting_value' => $stripeKey, 'data_type' => 'STRING', 'is_public' => true]
            );
            SiteSetting::updateOrCreate(
                ['tenant_id' => $tenant->id, 'setting_key' => 'stripe_secret'],
                ['setting_value' => $stripeSecret, 'data_type' => 'STRING', 'is_public' => false]
            );
        }
    }

    public function down(): void
    {
        SiteSetting::whereIn('setting_key', ['stripe_key', 'stripe_secret'])->delete();
    }
};
