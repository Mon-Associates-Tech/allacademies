<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamConfiguredParticipant;
use App\MockExam\Models\MockExamParticipant;

class MockExamParticipantService
{
    // ─── Configured participants ──────────────────────────────────────────────

    public function registerConfiguredParticipant(MockExam $exam, array $payload): MockExamConfiguredParticipant
    {
        return MockExamConfiguredParticipant::updateOrCreate(
            [
                'mock_exam_id' => $exam->id,
                'email'        => strtolower(trim((string) ($payload['email'] ?? ''))),
            ],
            [
                'name'        => trim((string) ($payload['name'] ?? '')),
                'unique_code' => $payload['unique_code'] ?? null,
                'is_active'   => true,
            ]
        );
    }

    /**
     * Import participants from an uploaded CSV file.
     * Expected columns: name, email, unique_code (header row optional).
     *
     * @return array{success: bool, imported: int, skipped: int}
     */
    public function importFromCsv(MockExam $exam, string $csvPath): array
    {
        $handle = fopen($csvPath, 'r');

        if (! $handle) {
            return ['success' => false, 'imported' => 0, 'skipped' => 0];
        }

        $imported = 0;
        $skipped  = 0;

        $header    = fgetcsv($handle);
        $hasHeader = is_array($header) && in_array('email', array_map('strtolower', $header), true);

        // If first row has no 'email' column header it is a data row – process it
        if (! $hasHeader && is_array($header)) {
            $this->upsertCsvRow($exam, $header) ? $imported++ : $skipped++;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $this->upsertCsvRow($exam, $row) ? $imported++ : $skipped++;
        }

        fclose($handle);

        return ['success' => true, 'imported' => $imported, 'skipped' => $skipped];
    }

    private function upsertCsvRow(MockExam $exam, array $row): bool
    {
        $name       = trim((string) ($row[0] ?? ''));
        $email      = strtolower(trim((string) ($row[1] ?? '')));
        $uniqueCode = trim((string) ($row[2] ?? ''));

        if ($email === '') {
            return false;
        }

        MockExamConfiguredParticipant::updateOrCreate(
            ['mock_exam_id' => $exam->id, 'email' => $email],
            [
                'name'        => $name !== '' ? $name : $email,
                'unique_code' => $uniqueCode !== '' ? $uniqueCode : null,
                'is_active'   => true,
            ]
        );

        return true;
    }

    // ─── Access authorisation ─────────────────────────────────────────────────

    /**
     * Determine whether the given credentials allow joining the exam.
     *
     * @return array{allowed: bool, mode: string, participant?: MockExamConfiguredParticipant|null, message?: string}
     */
    public function authorizeJoin(MockExam $exam, string $name, string $email, ?string $uniqueCode = null): array
    {
        $email      = strtolower(trim($email));
        $uniqueCode = $uniqueCode ? trim($uniqueCode) : null;

        // General mode – anyone can join
        if ($exam->participant_mode === 'general') {
            return ['allowed' => true, 'mode' => 'general'];
        }

        // Look up a configured participant
        $query = $exam->configuredParticipants()->where('is_active', true);

        if (($exam->configured_match_mode ?? 'any') === 'both') {
            $query->where('email', $email)->where('unique_code', $uniqueCode);
        } else {
            $query->where(function ($q) use ($email, $uniqueCode) {
                if ($email !== '') {
                    $q->where('email', $email);
                }
                if ($uniqueCode !== null && $uniqueCode !== '') {
                    $q->orWhere('unique_code', $uniqueCode);
                }
            });
        }

        $configured = $query->first();

        if (! $configured) {
            return [
                'allowed' => false,
                'mode'    => 'configured',
                'message' => 'You are not on the participant list for this examination.',
            ];
        }

        return [
            'allowed'     => true,
            'mode'        => 'configured',
            'participant' => $configured,
        ];
    }

    // ─── General participant management ───────────────────────────────────────

    /**
     * Find or create a MockExamParticipant by email, updating the name if changed.
     */
    public function createOrReuseParticipant(
        string $name,
        string $email,
        bool $autoVerify = true
    ): MockExamParticipant {
        $email = strtolower(trim($email));
        $name  = trim($name);

        $participant = MockExamParticipant::findByEmail($email);

        if ($participant) {
            if ($name !== '' && $participant->name !== $name) {
                $participant->update(['name' => $name]);
            }

            return $participant;
        }

        return MockExamParticipant::create([
            'name'             => $name ?: $email,
            'email'            => $email,
            'email_verified_at'=> $autoVerify ? now() : null,
        ]);
    }
}
