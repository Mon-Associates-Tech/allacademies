<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\GeneralExamParticipantGroup;
use App\ExaminationHub\Models\GeneralExamParticipantGroupMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ParticipantGroupService
{
    public function createGroup(string $name, ?string $description = null): GeneralExamParticipantGroup
    {
        return GeneralExamParticipantGroup::create([
            'name' => strtoupper($name),
            'description' => $description,
        ]);
    }

    public function updateGroup(GeneralExamParticipantGroup $group, string $name, ?string $description = null): GeneralExamParticipantGroup
    {
        $group->update([
            'name' => strtoupper($name),
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
            'name' => strtoupper($name),
            'email' => strtolower($email),
            'unique_code' => $uniqueCode,
        ]);
    }

    public function updateMember(GeneralExamParticipantGroupMember $member, string $name, string $email, ?string $uniqueCode = null): GeneralExamParticipantGroupMember
    {
        $member->update([
            'name' => strtoupper($name),
            'email' => strtolower($email),
            'unique_code' => $uniqueCode,
        ]);

        return $member->fresh();
    }

    public function deleteMember(GeneralExamParticipantGroupMember $member): void
    {
        $member->delete();
    }

    /**
     * Accept either an uploaded file instance or a filesystem path.
     * Maatwebsite Excel can detect the reader from the UploadedFile's original name.
     *
     * @param mixed $fileOrPath
     * @return array
     */
    public function importFromCsv($fileOrPath): array
    {
        try {
            $sheets = Excel::toArray(null, $fileOrPath);
        } catch (\Throwable $e) {
            return ['success' => false, 'imported' => 0, 'errors' => ['Could not read the uploaded spreadsheet: ' . $e->getMessage()]];
        }

        if (empty($sheets) || !isset($sheets[0])) {
            return ['success' => false, 'imported' => 0, 'errors' => ['The spreadsheet appears to be empty.']];
        }

        $rows = $sheets[0];
        if (!is_array($rows) || count($rows) === 0) {
            return ['success' => false, 'imported' => 0, 'errors' => ['The spreadsheet appears to be empty.']];
        }

        $rawHeader = $rows[0];
        if (!is_array($rawHeader)) {
            return ['success' => false, 'imported' => 0, 'errors' => ['The spreadsheet header row is invalid.']];
        }

        $header = array_map(fn($col) => strtolower(trim((string)$col)), $rawHeader);

        $nameIndex = array_search('name', $header, true);
        $emailIndex = array_search('email', $header, true);
        $groupIndex = array_search('group', $header, true);
        $codeIndex = array_search('unique_code', $header, true);
        $programmeIndex = array_search('programme', $header, true);

        if ($nameIndex === false || $emailIndex === false || $groupIndex === false || $programmeIndex === false) {
            return ['success' => false, 'imported' => 0, 'errors' => ['Missing required columns: name, email, group, programme']];
        }

        $imported = 0;
        $errors = [];
        $groups = [];
        $programmes = [];

        DB::beginTransaction();

        try {
            $totalRows = count($rows);
            for ($i = 1; $i < $totalRows; $i++) {
                $rowNumber = $i + 1;
                $row = $rows[$i];

                if (!is_array($row)) {
                    $errors[] = "Row {$rowNumber}: invalid row format.";
                    continue;
                }

                $name = trim((string)($row[$nameIndex] ?? ''));
                $email = strtolower(trim((string)($row[$emailIndex] ?? '')));
                $groupName = trim((string)($row[$groupIndex] ?? ''));
                $programmeName = trim((string)($row[$programmeIndex] ?? ''));
                $uniqueCode = $codeIndex !== false ? trim((string)($row[$codeIndex] ?? '')) : '';

                if ($name === '' || $email === '' || $groupName === '' || $programmeName === '') {
                    $errors[] = "Row {$rowNumber}: name, email, group and programme are required.";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: '{$email}' is not a valid email address.";
                    continue;
                }

                $groupNameUpper = strtoupper($groupName);
                $programmeNameUpper = strtoupper($programmeName);

                // Create or reuse parent group (uppercase)
                if (!isset($groups[$groupNameUpper])) {
                    $groups[$groupNameUpper] = GeneralExamParticipantGroup::firstOrCreate(
                        ['name' => $groupNameUpper],
                        ['description' => "Imported from CSV on " . now()->format('Y-m-d')]
                    );
                }

                $parent = $groups[$groupNameUpper];

                // Lookup programme by name under this parent only
                $programmeKey = $parent->id . '||' . $programmeNameUpper;
                if (!isset($programmes[$programmeKey])) {
                    $existing = GeneralExamParticipantGroup::where('name', $programmeNameUpper)
                        ->where('parent_id', $parent->id)
                        ->first();

                    if ($existing) {
                        $programmes[$programmeKey] = $existing;
                    } else {
                        $programmes[$programmeKey] = GeneralExamParticipantGroup::create([
                            'name' => $programmeNameUpper,
                            'description' => "Imported programme from CSV on " . now()->format('Y-m-d'),
                            'parent_id' => $parent->id,
                        ]);
                    }
                }

                $programme = $programmes[$programmeKey];

                // Create or update member attached to programme (programme id stored in group_id)
                GeneralExamParticipantGroupMember::updateOrCreate(
                    ['group_id' => $programme->id, 'email' => $email],
                    ['name' => strtoupper($name), 'unique_code' => $uniqueCode !== '' ? $uniqueCode : null]
                );

                $imported++;
            }

            DB::commit();

            return ['success' => true, 'imported' => $imported, 'errors' => $errors, 'groups' => count($groups)];
        } catch (\Exception $e) {
            DB::rollBack();
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
