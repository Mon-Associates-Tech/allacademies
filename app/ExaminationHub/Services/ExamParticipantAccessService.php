<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Contracts\ExamParticipantAccessServiceInterface;
use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamConfiguredParticipant;
use App\ExaminationHub\Models\GeneralExamParticipant;
use App\ExaminationHub\Support\ParticipantAuthHelper;

class ExamParticipantAccessService implements ExamParticipantAccessServiceInterface
{
    public function registerConfiguredParticipant(GeneralExam $exam, array $payload): array
    {
        $name     = (string) ($payload['name'] ?? '');
        $centerId = $payload['center_id'] ?? config('center.center_id');

        $record = GeneralExamConfiguredParticipant::updateOrCreate(
            [
                'general_exam_id' => $exam->id,
                'email' => strtolower((string) ($payload['email'] ?? '')),
            ],
            [
                'name'        => $name,
                'auth'        => ParticipantAuthHelper::generate($name),
                'unique_code' => $payload['unique_code'] ?? null,
                'center_id'   => $centerId !== null ? (int) $centerId : null,
                'is_active'   => true,
            ]
        );

        return ['success' => true, 'record' => $record];
    }

    /** @return array{success: bool, imported: int, errors: string[]} */
    public function importConfiguredParticipants(GeneralExam $exam, string $csvPath): array
    {
        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            return ['success' => false, 'imported' => 0, 'errors' => ['Could not open the uploaded file.']];
        }

        $rawHeader = fgetcsv($handle);
        if (! is_array($rawHeader)) {
            fclose($handle);

            return ['success' => false, 'imported' => 0, 'errors' => ['The CSV file appears to be empty.']];
        }

        $header = array_map(fn (string $col) => strtolower(trim($col)), $rawHeader);

        $firstNameIndex = array_search('first_name', $header, true);
        $lastNameIndex = array_search('last_name', $header, true);
        $nameIndex = array_search('name', $header, true);
        $emailIndex = array_search('email', $header, true);
        $codeIndex = array_search('unique_code', $header, true);
        $centerIndex = array_search('center_id', $header, true);

        // Validate required columns
        $hasNameSource = $firstNameIndex !== false && $lastNameIndex !== false || $nameIndex !== false;
        if (! $hasNameSource || $emailIndex === false) {
            fclose($handle);
            $missing = implode(', ', array_filter([
                ! $hasNameSource ? '(first_name + last_name) or name' : null,
                $emailIndex === false ? 'email' : null,
            ]));

            return ['success' => false, 'imported' => 0, 'errors' => ["Missing required column(s): {$missing}. Expected header: (first_name, last_name) or name, email, unique_code"]];
        }

        $imported = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Determine name from first_name + last_name or name field
            $name = '';
            if ($firstNameIndex !== false && $lastNameIndex !== false) {
                $firstName = trim((string) ($row[$firstNameIndex] ?? ''));
                $lastName = trim((string) ($row[$lastNameIndex] ?? ''));
                $name = trim("{$firstName} {$lastName}");
            }

            if ($name === '' && $nameIndex !== false) {
                $name = trim((string) ($row[$nameIndex] ?? ''));
            }

            $email = strtolower(trim((string) ($row[$emailIndex] ?? '')));
            $uniqueCode = $codeIndex !== false ? trim((string) ($row[$codeIndex] ?? '')) : '';
            $centerId = $centerIndex !== false ? trim((string) ($row[$centerIndex] ?? '')) : '';
            $centerId = $centerId !== '' ? (int) $centerId : null;

            if ($name === '') {
                $errors[] = "Row {$rowNumber}: name is required (provide either first_name + last_name or name column).";

                continue;
            }

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: '{$email}' is not a valid email address.";

                continue;
            }

            $centerId = $centerId ?? (int) config('center.center_id') ?: null;

            GeneralExamConfiguredParticipant::updateOrCreate(
                ['general_exam_id' => $exam->id, 'email' => $email],
                [
                    'name'        => $name,
                    'auth'        => ParticipantAuthHelper::generate($name),
                    'unique_code' => $uniqueCode !== '' ? $uniqueCode : null,
                    'center_id'   => $centerId,
                    'is_active'   => true,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return ['success' => true, 'imported' => $imported, 'errors' => $errors];
    }

    public function authorizeJoinByCode(GeneralExam $exam, string $name, string $email, ?string $uniqueCode = null): array
    {
        $uniqueCode = $uniqueCode ? trim($uniqueCode) : null;

        // The email field accepts either a real email address or a 6-character auth code.
        $rawEmail    = trim($email);
        $inputIsAuth = $rawEmail !== '' && ! str_contains($rawEmail, '@') && strlen($rawEmail) === 6;
        $email       = $inputIsAuth ? '' : strtolower($rawEmail);
        $authCode    = $inputIsAuth ? strtoupper($rawEmail) : null;

        if ($exam->participant_mode === 'general') {
            return ['allowed' => true, 'mode' => 'general'];
        }

        $configured       = null;
        $hasEmailForMatch = $email !== '';
        $hasAuthForMatch  = $authCode !== null;
        $hasCodeForMatch  = $uniqueCode !== null && $uniqueCode !== '';

        if ($exam->participant_mode === 'configured' || $exam->participant_mode === 'both') {
            $query               = $exam->configuredParticipants()->where('is_active', true);
            $canLookupConfigured = false;

            if (($exam->configured_match_mode ?? 'any') === 'both') {
                if (($hasEmailForMatch || $hasAuthForMatch) && $hasCodeForMatch) {
                    if ($hasAuthForMatch) {
                        $query->where('auth', $authCode);
                    } else {
                        $query->where('email', $email);
                    }
                    $query->where('unique_code', $uniqueCode);
                    $canLookupConfigured = true;
                }
            } else {
                if ($hasEmailForMatch || $hasAuthForMatch || $hasCodeForMatch) {
                    if ($hasAuthForMatch) {
                        $query->where('auth', $authCode);
                    } elseif ($hasEmailForMatch) {
                        $query->where('email', $email);
                    }
                    if ($hasCodeForMatch) {
                        $query->where('unique_code', $uniqueCode);
                    }
                    $canLookupConfigured = true;
                }
            }

            if ($canLookupConfigured) {
                $configured = $query->first();
            }
        }

        if ($exam->participant_mode === 'configured') {
            if (! $hasEmailForMatch && ! $hasAuthForMatch) {
                return ['allowed' => false, 'mode' => 'configured', 'message' => 'Please enter your email address or auth code.'];
            }

            $codeQuery = $exam->configuredParticipants()->where('is_active', true);

            if ($hasAuthForMatch) {
                $codeQuery->where('auth', $authCode);
            } else {
                $codeQuery->where('email', $email);
            }

            if ($hasCodeForMatch) {
                $codeQuery->where('unique_code', $uniqueCode);
            }

            $configured = $codeQuery->first();

            if (! $configured) {
                return ['allowed' => false, 'mode' => 'configured', 'message' => 'You are not on the configured participant list for this examination.'];
            }

            return ['allowed' => true, 'mode' => 'configured', 'configured_participant' => $configured];
        }

        if ($exam->participant_mode === 'both') {
            return ['allowed' => true, 'mode' => $configured ? 'configured' : 'general', 'configured_participant' => $configured];
        }

        return ['allowed' => false, 'message' => 'Unknown participant mode.'];
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
