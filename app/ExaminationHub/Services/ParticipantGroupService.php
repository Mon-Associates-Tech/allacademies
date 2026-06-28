<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExamParticipantGroup;
use App\ExaminationHub\Models\GeneralExamParticipantGroupMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ParticipantGroupService
{
    public function createGroup(string $name, ?string $description = null): GeneralExamParticipantGroup
    {
        return GeneralExamParticipantGroup::create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function updateGroup(GeneralExamParticipantGroup $group, string $name, ?string $description = null): GeneralExamParticipantGroup
    {
        $group->update([
            'name' => $name,
            'description' => $description,
        ]);

        return $group->fresh();
    }

    public function deleteGroup(GeneralExamParticipantGroup $group): void
    {
        $group->delete();
    }

    public function addMember(GeneralExamParticipantGroup $group, string $name, string $email, ?string $uniqueCode = null): GeneralExamParticipantGroupMember
    {
        return $group->members()->create([
            'name' => $name,
            'email' => strtolower($email),
            'unique_code' => $uniqueCode,
        ]);
    }

    public function updateMember(GeneralExamParticipantGroupMember $member, string $name, string $email, ?string $uniqueCode = null): GeneralExamParticipantGroupMember
    {
        $member->update([
            'name' => $name,
            'email' => strtolower($email),
            'unique_code' => $uniqueCode,
        ]);

        return $member->fresh();
    }

    public function deleteMember(GeneralExamParticipantGroupMember $member): void
    {
        $member->delete();
    }

    public function importFromCsv(string $csvPath): array
    {
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return ['success' => false, 'imported' => 0, 'errors' => ['Could not open the uploaded file.']];
        }

        $rawHeader = fgetcsv($handle);
        if (!is_array($rawHeader)) {
            fclose($handle);
            return ['success' => false, 'imported' => 0, 'errors' => ['The CSV file appears to be empty.']];
        }

        $header = array_map(fn(string $col) => strtolower(trim($col)), $rawHeader);

        $nameIndex = array_search('name', $header, true);
        $emailIndex = array_search('email', $header, true);
        $groupIndex = array_search('group', $header, true);
        $codeIndex = array_search('unique_code', $header, true);

        if ($nameIndex === false || $emailIndex === false || $groupIndex === false) {
            fclose($handle);
            return ['success' => false, 'imported' => 0, 'errors' => ['Missing required columns: name, email, group']];
        }

        $imported = 0;
        $errors = [];
        $rowNumber = 1;
        $groups = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                $name = trim((string)($row[$nameIndex] ?? ''));
                $email = strtolower(trim((string)($row[$emailIndex] ?? '')));
                $groupName = trim((string)($row[$groupIndex] ?? ''));
                $uniqueCode = $codeIndex !== false ? trim((string)($row[$codeIndex] ?? '')) : '';

                if ($name === '' || $email === '' || $groupName === '') {
                    $errors[] = "Row {$rowNumber}: name, email, and group are required.";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: '{$email}' is not a valid email address.";
                    continue;
                }

                if (!isset($groups[$groupName])) {
                    $groups[$groupName] = GeneralExamParticipantGroup::firstOrCreate(
                        ['name' => $groupName],
                        ['description' => "Imported from CSV on " . now()->format('Y-m-d')]
                    );
                }

                $groups[$groupName]->members()->updateOrCreate(
                    ['group_id' => $groups[$groupName]->id, 'email' => $email],
                    ['name' => $name, 'unique_code' => $uniqueCode !== '' ? $uniqueCode : null]
                );

                $imported++;
            }

            DB::commit();
            fclose($handle);

            return ['success' => true, 'imported' => $imported, 'errors' => $errors, 'groups' => count($groups)];
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return ['success' => false, 'imported' => 0, 'errors' => ['Import failed: ' . $e->getMessage()]];
        }
    }

    public function getAllGroups()
    {
        return GeneralExamParticipantGroup::withCount('members')
            ->with('creator')
            ->orderBy('name')
            ->paginate();
    }

    public function getGroupWithMembers(GeneralExamParticipantGroup $group): GeneralExamParticipantGroup
    {
        return $group->load(['members' => fn($q) => $q->orderBy('name')]);
    }

    public function copyGroupMembersToExam(GeneralExamParticipantGroup $group, int $examId): int
    {
        $copied = 0;

        foreach ($group->members as $member) {
            DB::table('general_exam_configured_participants')->updateOrInsert(
                [
                    'general_exam_id' => $examId,
                    'email' => $member->email,
                ],
                [
                    'name' => $member->name,
                    'unique_code' => $member->unique_code,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $copied++;
        }

        return $copied;
    }
}
