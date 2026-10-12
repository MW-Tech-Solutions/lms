<?php

declare(strict_types=1);

use App\Services\AuditService;
use App\Services\ComplianceRecertificationEngine;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('compliance:recertification-scan', function (ComplianceRecertificationEngine $engine) {
    $this->info('Starting automated 365-day workforce compliance recertification scan...');
    $results = $engine->checkAndProcessRecertifications();
    
    AuditService::log('SCHEDULED_COMPLIANCE_SCAN', null, null, $results);
    
    $this->info(sprintf(
        'Scan complete: %d certifications expired/re-enrolled, %d approaching deadline warnings registered.',
        $results['expired_count'],
        $results['warning_count']
    ));
})->purpose('Run automated 365-day enterprise compliance recertification loop');

// Schedule nightly compliance scan at 00:00 UTC
Schedule::command('compliance:recertification-scan')->dailyAt('00:00');
