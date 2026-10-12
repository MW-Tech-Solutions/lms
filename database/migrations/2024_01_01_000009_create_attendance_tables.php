<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->date('session_date');
            $table->string('qr_secret', 64);
            $table->decimal('lat_boundary', 10, 7)->nullable();
            $table->decimal('lng_boundary', 10, 7)->nullable();
            $table->unsignedInteger('radius_meters')->default(50);
            $table->timestamps();

            $table->index(['course_id', 'session_date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['PRESENT', 'ABSENT', 'LATE', 'EXCUSED'])->default('PRESENT');
            $table->enum('checkin_method', ['QR_SCAN', 'MANUAL_OVERRIDE', 'ZOOM_WEBHOOK'])->default('QR_SCAN');
            $table->string('verified_ip', 45)->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
    }
};
