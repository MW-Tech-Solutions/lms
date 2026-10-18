<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('certificates')) {
            if (!Schema::hasColumn('certificates', 'certificate_number')) {
                Schema::table('certificates', function (Blueprint $table) {
                    $table->string('certificate_number', 64)->nullable()->after('uuid');
                });
            }

            // Deduplicate any existing duplicate certificates (keep lowest id)
            $duplicates = DB::table('certificates')
                ->select('user_id', 'course_id', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as total'))
                ->groupBy('user_id', 'course_id')
                ->having('total', '>', 1)
                ->get();

            foreach ($duplicates as $dup) {
                DB::table('certificates')
                    ->where('user_id', $dup->user_id)
                    ->where('course_id', $dup->course_id)
                    ->where('id', '!=', $dup->keep_id)
                    ->delete();
            }

            // Populate certificate_number for any existing records
            $certs = DB::table('certificates')->whereNull('certificate_number')->get();
            foreach ($certs as $c) {
                $issued = !empty($c->issued_at) ? new \DateTime($c->issued_at) : new \DateTime();
                $certNum = sprintf('ATC-%s-%s', $issued->format('Y-md'), str_pad((string) $c->id, 3, '0', STR_PAD_LEFT));
                DB::table('certificates')->where('id', $c->id)->update(['certificate_number' => $certNum]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('certificates') && Schema::hasColumn('certificates', 'certificate_number')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('certificate_number');
            });
        }
    }
};
