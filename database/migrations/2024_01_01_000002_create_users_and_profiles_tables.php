<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type', ['EMPLOYEE', 'STUDENT', 'HYBRID'])->default('STUDENT');
            $table->string('employee_id')->nullable()->index();
            $table->text('two_factor_secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->index(['tenant_id', 'user_type']);
            $table->index(['department_id', 'is_active']);
        });

        // Add foreign key constraint to departments.manager_user_id
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('avatar_url')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('locale')->default('en');
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['manager_user_id']);
        });
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');
    }
};
