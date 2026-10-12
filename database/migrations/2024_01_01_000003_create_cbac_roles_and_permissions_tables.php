<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contexts', function (Blueprint $table) {
            $table->id();
            $table->enum('context_level', ['SYSTEM', 'TENANT', 'DEPARTMENT', 'CATEGORY', 'COURSE', 'MODULE']);
            $table->string('instance_id', 64)->nullable()->index();
            $table->string('path', 255)->index(); // Materialized path for hierarchical traversal e.g. "1/5/12"
            $table->timestamps();

            $table->index(['context_level', 'instance_id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 96)->unique();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('context_id')->constrained('contexts')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'context_id']);
            $table->unique(['user_id', 'role_id', 'context_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('contexts');
    }
};
