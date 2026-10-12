<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('setting_key', 128);
            $table->longText('setting_value');
            $table->enum('data_type', ['STRING', 'JSON', 'COLOR', 'MEDIA_URL'])->default('STRING');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'setting_key']);
            $table->index(['setting_key', 'is_public']);
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['slug', 'is_published']);
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('cms_pages')->cascadeOnDelete();
            $table->enum('section_type', ['HERO', 'METRICS', 'CATALOG', 'PRICING', 'DYNAMIC_FORM', 'FOOTER', 'CUSTOM_HTML']);
            $table->integer('order_index')->default(0);
            $table->json('content_json');
            $table->json('styles_json')->nullable();
            $table->timestamps();

            $table->index(['page_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('site_settings');
    }
};
