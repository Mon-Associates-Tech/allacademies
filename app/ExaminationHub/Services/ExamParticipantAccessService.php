<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Contracts\ExamParticipantAccessServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamParticipant;

class ExamParticipantAccessService implements ExamParticipantAccessServiceInterface
{
    public function registerConfiguredParticipant(GeneralExam $exam, array $payload): array
    {
        $record = GeneralExamConfiguredParticipant::updateOrCreate(
            [
                'general_exam_id' => $exam->id,
                'email' => strtolower((string) ($payload['email'] ?? '')),
            ],
            [
                'name' => (string) ($payload['name'] ?? ''),
                'unique_code' => $payload['unique_code'] ?? null,
                'is_active' => true,
            ]
        );

        return ['success' => true, 'record' => $record];
    }

    public function importConfiguredParticipants(GeneralExam $exam, string $csvPath): array
    {
        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            return ['success' => false, 'imported' => 0];
        }

        $imported = 0;
        $header = fgetcsv($handle);
        $hasHeader = is_array($header) && in_array('email', array_map('strtolower', $header), true);
        if (! $hasHeader && is_array($header)) {
            $this->upsertCsvRow($exam, $header);
            $imported++;
        }

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->upsertCsvRow($exam, $row)) {
                $imported++;
            }
        }

        fclose($handle);

        return ['success' => true, 'imported' => $imported];
    }

    private function upsertCsvRow(GeneralExam $exam, array $row): bool
    {
        $name = trim((string) ($row[0] ?? ''));
        $email = strtolower(trim((string) ($row[1] ?? '')));
        $uniqueCode = trim((string) ($row[2] ?? ''));

        if ($email === '') {
            return false;
        }

        GeneralExamConfiguredParticipant::updateOrCreate(
            ['general_exam_id' => $exam->id, 'email' => $email],
            ['name' => $name !== '' ? $name : $email, 'unique_code' => $uniqueCode !== '' ? $uniqueCode : null, 'is_active' => true]
        );

        return true;
    }

    public function authorizeJoinByCode(GeneralExam $exam, string $name, string $email, ?string $uniqueCode = null): array
    {
        $email = strtolower(trim($email));
        $uniqueCode = $uniqueCode ? trim($uniqueCode) : null;

        if ($exam->participant_mode === 'general') {
            return ['allowed' => true, 'mode' => 'general'];
        }

        $configured = null;
        if ($exam->participant_mode === 'configured' || $exam->participant_mode === 'both') {
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
        }

        if ($exam->participant_mode === 'configured' && ! $configured) {
            return ['allowed' => false, 'mode' => 'configured', 'message' => 'You are not on the configured participant list for this examination.'];
        }

        if ($exam->participant_mode === 'both') {
            return ['allowed' => true, 'mode' => $configured ? 'configured' : 'general', 'configured_participant' => $configured];
        }

        return ['allowed' => true, 'mode' => 'configured', 'configured_participant' => $configured];
    }

    public function createOrReuseParticipant(string $name, string $email): GeneralExamParticipant
    {
        $email = strtolower(trim($email));
        $name = trim($name);

        $participant = GeneralExamParticipant::where('email', $email)->first();
        if ($participant) {
            if ($name !== '' && $participant->name !== $name) {
                $participant->update(['name' => $name]);
            }

            return $participant;
        }

        return GeneralExamParticipant::create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
        ]);
    }
}
