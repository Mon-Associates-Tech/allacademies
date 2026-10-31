<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExamSyncState;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamQuestion;
use App\ExaminationHub\Models\GeneralExamSection;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Traits\HasUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PullAgent — pulls exam packages from central on centers.
 *
 * This agent fetches the manifest, compares with local state, downloads
 * missing revisions, verifies checksum and signature, and applies them.
 */
class PullAgent
{
    /**
     * Execute a pull sync.
     *
     * @return array{success: bool, pulled: int, errors: string[]}
     */
    public function pull(): array
    {
        $centralUrl = config('center.central_url');
        $token = config('center.sync_token');
        $publicKey = config('center.central_public_key');

        if (!$centralUrl || !$token) {
            return [
                'success' => false,
                'pulled' => 0,
                'errors' => ['Central URL or sync token not configured.'],
            ];
        }

        $syncRun = SyncRun::create([
            'direction' => 'pull',
            'started_at' => now(),
            'status' => 'running',
        ]);

        $errors = [];
        $pulled = 0;

        try {
            // Fetch manifest
            $manifest = $this->fetchManifest($centralUrl, $token);

            if (!$manifest) {
                throw new \RuntimeException('Failed to fetch manifest from central.');
            }

            // Check clock skew
            $serverTime = \Illuminate\Support\Carbon::parse($manifest['server_time'] ?? null);
            if ($serverTime) {
                $skew = abs(now()->diffInSeconds($serverTime));
                $threshold = config('center.clock_skew_threshold_seconds', 120);

                if ($skew > $threshold) {
                    Log::warning("Clock skew detected: {$skew}s exceeds threshold {$threshold}s");
                    // Don't block pull, but log the warning
                }
            }

            // Process each exam in the manifest
            foreach ($manifest['exams'] ?? [] as $examInfo) {
                try {
                    $result = $this->pullExam($centralUrl, $token, $publicKey, $examInfo);
                    if ($result['success']) {
                        $pulled++;
                    }
                    if (!empty($result['errors'])) {
                        $errors = array_merge($errors, $result['errors']);
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to pull exam {$examInfo['uuid']}: {$e->getMessage()}";
                    Log::error('PullAgent: Failed to pull exam', [
                        'exam_uuid' => $examInfo['uuid'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $syncRun->update([
                'finished_at' => now(),
                'status' => empty($errors) ? 'success' : 'failed',
                'items_total' => count($manifest['exams'] ?? []),
                'items_ok' => $pulled,
                'error' => implode('; ', array_slice($errors, 0, 5)),
            ]);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $syncRun->update([
                'finished_at' => now(),
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'success' => empty($errors),
            'pulled' => $pulled,
            'errors' => $errors,
        ];
    }

    /**
     * Fetch the manifest from central.
     */
    protected function fetchManifest(string $centralUrl, string $token): ?array
    {
        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->get("{$centralUrl}/api/sync/v1/manifest");

            if (!$response->successful()) {
                Log::error("PullAgent: Manifest fetch failed with status {$response->status()}");
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("PullAgent: Failed to connect to central: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Pull a single exam package.
     *
     * @param array{uuid: string, revision: int, checksum: string, size: int} $examInfo
     * @return array{success: bool, errors: string[]}
     */
    protected function pullExam(string $centralUrl, string $token, string $publicKey, array $examInfo): array
    {
        $examUuid = $examInfo['uuid'];
        $remoteRevision = (int) $examInfo['revision'];
        $remoteChecksum = $examInfo['checksum'];

        // Check local state
        $localState = ExamSyncState::where('exam_uuid', $examUuid)->first();
        $localRevision = $localState?->applied_revision ?? 0;

        // Skip if already up to date
        if ($localRevision >= $remoteRevision) {
            return ['success' => true, 'errors' => []];
        }

        // Download the package
        $payload = $this->downloadPackage($centralUrl, $token, $examUuid, $remoteRevision);
        if (!$payload) {
            return ['success' => false, 'errors' => ["Failed to download package for {$examUuid} revision {$remoteRevision}."]];
        }

        // Verify checksum
        $decompressed = @gzdecode($payload);
        if ($decompressed === false) {
            return ['success' => false, 'errors' => ["Failed to decompress package for {$examUuid}."]];
        }

        $calculatedChecksum = hash('sha256', $decompressed);
        if (!hash_equals($remoteChecksum, $calculatedChecksum)) {
            return ['success' => false, 'errors' => ["Checksum mismatch for {$examUuid} revision {$remoteRevision}."]];
        }

        // Verify signature
        $packageData = json_decode($decompressed, true);
        if (!$packageData) {
            return ['success' => false, 'errors' => ["Invalid JSON in package for {$examUuid}."]];
        }

        $signature = base64_decode($packageData['signature'] ?? '');
        $publicKeyBytes = base64_decode($publicKey);

        if ($signature === false || $publicKeyBytes === false) {
            return ['success' => false, 'errors' => ["Invalid signature encoding for {$examUuid}."]];
        }

        if (!\Sodium\crypto_sign_verify_detached($signature, $calculatedChecksum, $publicKeyBytes)) {
            return ['success' => false, 'errors' => ["Signature verification failed for {$examUuid} revision {$remoteRevision}."]];
        }

        // Apply the package
        $applier = new ExamPackageApplier();
        $applyResult = $applier->apply($packageData);

        if (!$applyResult['success']) {
            return $applyResult;
        }

        // Update local sync state
        ExamSyncState::updateOrCreate(
            ['exam_uuid' => $examUuid],
            [
                'applied_revision' => $remoteRevision,
                'applied_state_version' => $packageData['state_version'] ?? 1,
                'checksum' => $remoteChecksum,
                'applied_at' => now(),
            ]
        );

        return ['success' => true, 'errors' => []];
    }

    /**
     * Download a package from central.
     */
    protected function downloadPackage(string $centralUrl, string $token, string $uuid, int $revision): ?string
    {
        try {
            $response = Http::withToken($token)
                ->timeout(120)
                ->get("{$centralUrl}/api/sync/v1/exams/{$uuid}/revisions/{$revision}");

            if (!$response->successful()) {
                Log::error("PullAgent: Package download failed with status {$response->status()}");
                return null;
            }

            return $response->body();
        } catch (\Exception $e) {
            Log::error("PullAgent: Failed to download package: {$e->getMessage()}");
            return null;
        }
    }
}
