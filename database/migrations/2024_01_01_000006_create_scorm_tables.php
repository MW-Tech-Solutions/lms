<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scorm_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->enum('version', ['1.2', '2004'])->default('1.2');
            $table->string('entry_url');
            $table->string('storage_path');
            $table->timestamps();
        });

        Schema::create('scorm_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('scorm_package_id')->constrained('scorm_packages')->cascadeOnDelete();
            $table->string('cmi_key', 128);
            $table->longText('cmi_value');
            $table->timestamps();

            $table->unique(['user_id', 'scorm_package_id', 'cmi_key'], 'scorm_user_pkg_key_unique');
            $table->index(['scorm_package_id', 'cmi_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scorm_tracks');
        Schema::dropIfExists('scorm_packages');
    }
};
