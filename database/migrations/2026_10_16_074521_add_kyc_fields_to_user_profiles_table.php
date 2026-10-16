<?php

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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->text('about_instructor')->nullable()->after('bio');
            $table->text('teaching_methodology')->nullable()->after('about_instructor');
            $table->text('qualifications')->nullable()->after('teaching_methodology');
            $table->string('kyc_status', 30)->default('PENDING')->after('qualifications'); // PENDING, SUBMITTED, VERIFIED, REJECTED
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'about_instructor',
                'teaching_methodology',
                'qualifications',
                'kyc_status',
                'kyc_submitted_at',
            ]);
        });
    }
};
