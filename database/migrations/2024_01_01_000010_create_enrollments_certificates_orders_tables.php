<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->enum('status', ['ACTIVE', 'COMPLETED', 'EXPIRED'])->default('ACTIVE');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Recertification deadline
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['status', 'expires_at']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'course_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('total_amount_cents');
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_gateway', ['STRIPE', 'PAYPAL'])->default('STRIPE');
            $table->string('payment_id')->nullable()->index();
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED', 'REFUNDED'])->default('COMPLETED');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('enrollments');
    }
};
