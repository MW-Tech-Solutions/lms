<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('users', 'two_factor_type')) {
                $table->string('two_factor_type', 32)->default('NONE')->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'two_factor_email_code')) {
                $table->string('two_factor_email_code', 16)->nullable()->after('two_factor_type');
            }
            if (!Schema::hasColumn('users', 'two_factor_expires_at')) {
                $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_email_code');
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'proposed_price_cents')) {
                $table->unsignedInteger('proposed_price_cents')->nullable()->after('price_cents');
            }
            if (!Schema::hasColumn('courses', 'special_pricing_tag')) {
                $table->string('special_pricing_tag', 64)->nullable()->after('proposed_price_cents');
            }
            if (!Schema::hasColumn('courses', 'price_approval_status')) {
                $table->string('price_approval_status', 32)->default('APPROVED')->after('special_pricing_tag');
            }
            if (!Schema::hasColumn('courses', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('price_approval_status');
            }
            if (!Schema::hasColumn('courses', 'material_approval_status')) {
                $table->string('material_approval_status', 32)->default('APPROVED')->after('material_notes');
            }
            if (!Schema::hasColumn('courses', 'material_rejection_reason')) {
                $table->text('material_rejection_reason')->nullable()->after('material_approval_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'rejection_reason',
                'two_factor_type',
                'two_factor_email_code',
                'two_factor_expires_at',
            ]);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'proposed_price_cents',
                'special_pricing_tag',
                'price_approval_status',
                'rejection_reason',
                'material_approval_status',
                'material_rejection_reason',
            ]);
        });
    }
};
