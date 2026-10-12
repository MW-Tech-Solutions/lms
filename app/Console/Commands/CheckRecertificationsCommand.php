<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComplianceRecertificationEngine;
use Illuminate\Console\Command;

class CheckRecertificationsCommand extends Command
{
    protected $signature = 'lms:check-recertifications';
    protected $description = 'Scan enterprise employee compliance tracks, expire overdue certifications, and trigger re-enrollment cycles';

    public function handle(ComplianceRecertificationEngine $engine): int
    {
        $this->info('Initializing Compliance Recertification Engine Scan...');

        $results = $engine->checkAndProcessRecertifications();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Expired & Re-enrolled', $results['expired_count']],
                ['30-Day Warning Notices', $results['warning_count']],
            ]
        );

        $this->info('Compliance Recertification check completed successfully.');

        return self::SUCCESS;
    }
}
