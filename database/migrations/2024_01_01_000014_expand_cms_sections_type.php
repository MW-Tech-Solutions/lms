<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to string so custom sections (CATEGORIES, MENTORS, EVENTS, FAQ) can be freely added and managed
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `cms_sections` MODIFY COLUMN `section_type` VARCHAR(64) NOT NULL");
        } else {
            Schema::table('cms_sections', function (Blueprint $table) {
                $table->string('section_type', 64)->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `cms_sections` MODIFY COLUMN `section_type` ENUM('HERO', 'METRICS', 'CATALOG', 'PRICING', 'DYNAMIC_FORM', 'FOOTER', 'CUSTOM_HTML') NOT NULL");
        }
    }
};
