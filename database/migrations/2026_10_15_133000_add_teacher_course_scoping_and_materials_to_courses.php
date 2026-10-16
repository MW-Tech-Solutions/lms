<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('category_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('courses', 'video_url')) {
                $table->string('video_url', 1000)->nullable()->after('thumbnail');
            }
            if (!Schema::hasColumn('courses', 'material_file')) {
                $table->string('material_file', 1000)->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('courses', 'material_notes')) {
                $table->longText('material_notes')->nullable()->after('material_file');
            }
            if (!Schema::hasColumn('courses', 'deletion_status')) {
                $table->string('deletion_status', 32)->default('NONE')->after('is_published');
            }
            if (!Schema::hasColumn('courses', 'deletion_reason')) {
                $table->text('deletion_reason')->nullable()->after('deletion_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn([
                'instructor_id',
                'video_url',
                'material_file',
                'material_notes',
                'deletion_status',
                'deletion_reason',
            ]);
        });
    }
};
