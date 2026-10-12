<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ScormPackage;
use App\Models\ScormTrack;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ScormRuntimeService
{
    /**
     * Ingest and extract a SCORM 1.2 or SCORM 2004 ZIP archive.
     */
    public function ingestPackage(UploadedFile $file, string $title, ?string $tenantId = null): ScormPackage
    {
        $tempPath = $file->getRealPath();
        $zip = new ZipArchive();

        if ($zip->open($tempPath) !== true) {
            throw new \RuntimeException('Failed to open SCORM zip archive.');
        }

        $packageDir = 'scorm/' . uniqid('pkg_', true);
        $fullExtractPath = storage_path('app/public/' . $packageDir);

        if (!is_dir($fullExtractPath) && !mkdir($fullExtractPath, 0755, true) && !is_dir($fullExtractPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $fullExtractPath));
        }

        $zip->extractTo($fullExtractPath);
        $zip->close();

        // Inspect imsmanifest.xml
        $manifestPath = $fullExtractPath . '/imsmanifest.xml';
        $version = '1.2';
        $entryPoint = 'index.html';

        if (file_exists($manifestPath)) {
            $xmlContent = file_get_contents($manifestPath);
            if ($xmlContent !== false) {
                if (str_contains($xmlContent, 'CAM 1.3') || str_contains($xmlContent, '2004')) {
                    $version = '2004';
                }
                $xml = @simplexml_load_string($xmlContent);
                if ($xml && isset($xml->resources->resource)) {
                    foreach ($xml->resources->resource as $res) {
                        if (isset($res['href'])) {
                            $entryPoint = (string) $res['href'];
                            break;
                        }
                    }
                }
            }
        }

        $entryUrl = Storage::url($packageDir . '/' . $entryPoint);

        return ScormPackage::create([
            'tenant_id' => $tenantId,
            'title' => $title,
            'version' => $version,
            'entry_url' => $entryUrl,
            'storage_path' => $packageDir,
        ]);
    }

    /**
     * Retrieve current CMI state for a learner and package.
     *
     * @return array<string, string>
     */
    public function initializeState(User $user, ScormPackage $package): array
    {
        $tracks = ScormTrack::where('user_id', $user->id)
            ->where('scorm_package_id', $package->id)
            ->get();

        $cmiData = [
            'cmi.core.student_id' => (string) $user->id,
            'cmi.core.student_name' => $user->name,
            'cmi.core.lesson_status' => 'not attempted',
            'cmi.core.entry' => 'ab-initio',
            'cmi.core.score.raw' => '0',
            'cmi.core.credit' => 'credit',
            'cmi.core.lesson_mode' => 'normal',
        ];

        foreach ($tracks as $track) {
            $cmiData[$track->cmi_key] = $track->cmi_value;
        }

        return $cmiData;
    }

    /**
     * Persist CMI data from LMSCommit call.
     *
     * @param array<string, string> $cmiPayload
     * @return array{success: bool, status: string, score: ?string}
     */
    public function commitState(User $user, ScormPackage $package, array $cmiPayload): array
    {
        foreach ($cmiPayload as $key => $value) {
            ScormTrack::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'scorm_package_id' => $package->id,
                    'cmi_key' => (string) $key,
                ],
                [
                    'cmi_value' => (string) $value,
                ]
            );
        }

        $lessonStatus = $cmiPayload['cmi.core.lesson_status']
            ?? $cmiPayload['cmi.completion_status']
            ?? 'incomplete';

        $score = $cmiPayload['cmi.core.score.raw']
            ?? $cmiPayload['cmi.score.raw']
            ?? null;

        return [
            'success' => true,
            'status' => (string) $lessonStatus,
            'score' => $score !== null ? (string) $score : null,
        ];
    }
}
