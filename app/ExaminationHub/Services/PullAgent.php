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
     * @return array{success: bool, pulled: int, errors: string[], details: array[]}
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
                'details' => [],
            ];
        }

        // Clean up any stale "running" records from crashed jobs
        SyncRun::cleanupStaleRunning(30);

        // Prevent overlapping pull runs — skip if a pull is already in progress
        $activePull = SyncRun::where('direction', 'pull')
            ->where('status', 'running')
            ->where('started_at', '>', now()->subMinutes(30))
            ->first();
        if ($activePull) {
            // Skip silently — another pull is already running
            return [
                'success' => true,
                'pulled' => 0,
                'errors' => [],
                'details' => [],
                'already_running' => true,
            ];
        }

        $syncRun = SyncRun::create([
            'direction' => 'pull',
            'started_at' => now(),
            'status' => 'running',
        ]);

        $errors = [];
        $pulled = 0;
        $details = [];

        try {
            // Fetch manifest
            $manifest = $this->fetchManifest($centralUrl, $token);

            if (!$manifest) {
                throw new \RuntimeException('Failed to fetch manifest from central.');
            }

            // Manifest fetched silently

            // Check clock skew
            $serverTime = \Illuminate\Support\Carbon::parse($manifest['server_time'] ?? null);
            if ($serverTime) {
                $skew = abs(now()->diffInSeconds($serverTime));
                $threshold = config('center.clock_skew_threshold_seconds', 120);

                if ($skew > $threshold) {
                    Log::warning("Clock skew detected: {$skew}s exceeds threshold {$threshold}s");
                    $details[] = [
                        'status' => 'warning',
                        'message' => "Clock skew detected: {$skew}s (threshold: {$threshold}s)",
                    ];
                }
            }

            // Process each exam in the manifest
            foreach ($manifest['exams'] ?? [] as $examInfo) {
                try {
                    $result = $this->pullExam($centralUrl, $token, $publicKey, $examInfo);
                    if ($result['success']) {
                        $pulled++;
                        $details[] = [
                            'status' => 'success',
                            'exam_uuid' => $examInfo['uuid'],
                            'revision' => $examInfo['revision'],
                            'message' => $result['message'] ?? 'Exam pulled successfully',
                            'sections' => $result['sections'] ?? 0,
                            'questions' => $result['questions'] ?? 0,
                            'participants' => $result['participants'] ?? 0,
                        ];
                    } else {
                        $details[] = [
                            'status' => 'error',
                            'exam_uuid' => $examInfo['uuid'],
                            'revision' => $examInfo['revision'],
                            'message' => implode(', ', $result['errors'] ?? ['Unknown error']),
                        ];
                    }
                    if (!empty($result['errors'])) {
                        $errors = array_merge($errors, $result['errors']);
                    }
                } catch (\Exception $e) {
                    $errorMsg = "Failed to pull exam {$examInfo['uuid']}: {$e->getMessage()}";
                    $errors[] = $errorMsg;
                    $details[] = [
                        'status' => 'error',
                        'exam_uuid' => $examInfo['uuid'],
                        'revision' => $examInfo['revision'],
                        'message' => $e->getMessage(),
                    ];
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

            // Pull completed — single summary log
            Log::info('PullAgent: Pull completed', [
                'pulled' => $pulled,
                'errors' => count($errors),
            ]);

            // Report pull status to central via heartbeat so last_pull_at is tracked
            $this->reportHeartbeat($centralUrl, $token);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            $details[] = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
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
            'details' => $details,
        ];
    }

    /**
     * Fetch the manifest from central.
     */
    protected function fetchManifest(string $centralUrl, string $token): ?array
    {
        try {
            $response = Http::withToken($token)
                ->withHeaders($this->syncHeaders($token))
                ->timeout(30)
                ->get("{$centralUrl}/api/sync/v1/manifest");

            if (!$response->successful()) {
                Log::error("PullAgent: Manifest fetch failed with status {$response->status()}", [
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("PullAgent: Failed to connect to central: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Build common sync API headers.
     */
    protected function syncHeaders(string $token): array
    {
        $appVersion = config('center.app_version', '1.0.0');
        return [
            'X-App-Version' => $appVersion,
            'X-Timestamp' => (string) time(),
            'X-Signature' => hash_hmac('sha256', '', $token),
        ];
    }

    /**
     * Report heartbeat to central so last_pull_at and last_seen_at are tracked.
     *
     * The center is identified by the bearer token (SyncAuth middleware resolves it).
     * No center_uuid needed in the body — the center doesn't know its own UUID.
     */
    protected function reportHeartbeat(string $centralUrl, string $token): void
    {
        try {
            $bodyArray = [
                'app_version' => config('center.app_version', '1.0.0'),
                'last_pull_at' => now()->toIso8601String(),
            ];
            $bodyString = json_encode($bodyArray);

            $signature = hash_hmac('sha256', $bodyString, $token);

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'X-Signature' => $signature,
                    'X-Timestamp' => (string) time(),
                    'X-App-Version' => config('center.app_version', '1.0.0'),
                    'Content-Type' => 'application/json',
                ])
                ->withBody($bodyString, 'application/json')
                ->post($centralUrl . '/api/sync/v1/heartbeat');

            if (!$response->successful()) {
                Log::warning("PullAgent: Heartbeat failed with status " . $response->status(), [
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("PullAgent: Heartbeat failed: " . $e->getMessage());
        }
    }

    /**
     * Pull a single exam package.
     *
     * @param array{uuid: string, revision: int, checksum: string, size: int} $examInfo
     * @return array{success: bool, errors: string[], message: string, sections: int, questions: int, participants: int}
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
            // Exam already up to date — no log needed
            return [
                'success' => true,
                'errors' => [],
                'message' => 'Already up to date (revision ' . $localRevision . ')',
                'sections' => 0,
                'questions' => 0,
                'participants' => 0,
            ];
        }

            // Downloading package silently

        // Download the package
        $payload = $this->downloadPackage($centralUrl, $token, $examUuid, $remoteRevision);
        if (!$payload) {
            $error = "Failed to download package for {$examUuid} revision {$remoteRevision}.";
            Log::error('PullAgent: ' . $error);
            return ['success' => false, 'errors' => [$error], 'message' => $error, 'sections' => 0, 'questions' => 0, 'participants' => 0];
        }

        // Decompress the package
        $decompressed = @gzdecode($payload);
        if ($decompressed === false) {
            $error = "Failed to decompress package for {$examUuid}.";
            Log::error('PullAgent: ' . $error);
            return ['success' => false, 'errors' => [$error], 'message' => $error, 'sections' => 0, 'questions' => 0, 'participants' => 0];
        }

        // Parse the package JSON
        $packageData = json_decode($decompressed, true);
        if (!$packageData) {
            $error = "Invalid JSON in package for {$examUuid}.";
            Log::error('PullAgent: ' . $error);
            return ['success' => false, 'errors' => [$error], 'message' => $error, 'sections' => 0, 'questions' => 0, 'participants' => 0];
        }

        // Verify checksum: compare the checksum embedded in the package with the manifest's checksum
        $embeddedChecksum = $packageData['checksum'] ?? '';
        if (!hash_equals($remoteChecksum, $embeddedChecksum)) {
            $error = "Checksum mismatch for {$examUuid} revision {$remoteRevision}.";
            Log::error('PullAgent: ' . $error, [
                'remote_checksum' => $remoteChecksum,
                'embedded_checksum' => $embeddedChecksum,
            ]);
            return ['success' => false, 'errors' => [$error], 'message' => $error, 'sections' => 0, 'questions' => 0, 'participants' => 0];
        }

            // Checksum verified silently

        // Verify signature: the signature was created over the checksum
        $signature = base64_decode($packageData['signature'] ?? '');
        $publicKeyBytes = base64_decode($publicKey);

        if ($signature === false || $publicKeyBytes === false) {
            $error = "Invalid signature encoding for {$examUuid}.";
            Log::error('PullAgent: ' . $error);
            return ['success' => false, 'errors' => [$error], 'message' => $error, 'sections' => 0, 'questions' => 0, 'participants' => 0];
        }

        if (!\sodium_crypto_sign_verify_detached($signature, $embeddedChecksum, $publicKeyBytes)) {
            $error = "Signature verification failed for {$examUuid} revision {$remoteRevision}.";
            Log::error('PullAgent: ' . $error);
            return ['success' => false, 'errors' => [$error], 'message' => $error, 'sections' => 0, 'questions' => 0, 'participants' => 0];
        }

            // Signature verified silently

        // Apply the package
            // Applying package silently
        $applier = new ExamPackageApplier();
        $applyResult = $applier->apply($packageData);

        if (!$applyResult['success']) {
            $errorMsg = "Failed to apply package for {$examUuid}: " . implode(', ', $applyResult['errors']);
            Log::error('PullAgent: ' . $errorMsg);
            return [
                'success' => false,
                'errors' => $applyResult['errors'],
                'message' => $errorMsg,
                'sections' => 0,
                'questions' => 0,
                'participants' => 0,
            ];
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

        $message = "Applied revision {$remoteRevision}: {$applyResult['sections']} sections, {$applyResult['questions']} questions, {$applyResult['participants']} participants";
        // Applied silently

        return [
            'success' => true,
            'errors' => [],
            'message' => $message,
            'sections' => $applyResult['sections'] ?? 0,
            'questions' => $applyResult['questions'] ?? 0,
            'participants' => $applyResult['participants'] ?? 0,
        ];
    }

    /**
     * Download a package from central.
     */
    protected function downloadPackage(string $centralUrl, string $token, string $uuid, int $revision): ?string
    {
        try {
            $response = Http::withToken($token)
                ->withHeaders($this->syncHeaders($token))
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
