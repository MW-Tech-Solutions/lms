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
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'approval_status')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY `approval_status` ENUM('APPROVED', 'PENDING', 'UNAPPROVED', 'REJECTED') NOT NULL DEFAULT 'PENDING'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'approval_status')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY `approval_status` ENUM('APPROVED', 'PENDING', 'UNAPPROVED', 'REJECTED') NOT NULL DEFAULT 'APPROVED'");
        }
    }
};
