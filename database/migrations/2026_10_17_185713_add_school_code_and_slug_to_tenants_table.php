<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'school_code')) {
                $table->string('school_code', 64)->nullable()->unique()->after('subdomain');
            }
            if (!Schema::hasColumn('tenants', 'slug')) {
                $table->string('slug', 100)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('tenants', 'admin_user_id')) {
                $table->foreignId('admin_user_id')->nullable()->after('branding_json')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tenants', 'subscription_status')) {
                $table->string('subscription_status', 30)->default('ACTIVE')->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'admin_user_id')) {
                $table->dropForeign(['admin_user_id']);
                $table->dropColumn('admin_user_id');
            }
            if (Schema::hasColumn('tenants', 'school_code')) {
                $table->dropColumn('school_code');
            }
            if (Schema::hasColumn('tenants', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('tenants', 'subscription_status')) {
                $table->dropColumn('subscription_status');
            }
        });
    }
};
