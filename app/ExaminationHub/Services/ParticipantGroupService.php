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

    private function normalizeString(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value));
    }

    private function normalizeName(string $value): string
    {
        return strtoupper($this->normalizeString($value));
    }

    private function normalizeEmail(string $value): string
    {
        return strtolower($this->normalizeString($value));
    }

    private function normalizeUniqueCode(string $value): ?string
    {
        $clean = $this->normalizeString($value);

        return $clean === '' ? null : $clean;
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

                $name = $this->normalizeName((string)($row[$nameIndex] ?? ''));
                $email = $this->normalizeEmail((string)($row[$emailIndex] ?? ''));
                $groupName = $this->normalizeString((string)($row[$groupIndex] ?? ''));
                $programmeName = $this->normalizeString((string)($row[$programmeIndex] ?? ''));
                $uniqueCode = $codeIndex !== false ? $this->normalizeUniqueCode((string)($row[$codeIndex] ?? '')) : null;

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

                // Create or reuse top-level parent group by normalized name.
                if (!isset($groups[$groupNameUpper])) {
                    $existingGroup = GeneralExamParticipantGroup::whereNull('parent_id')
                        ->whereRaw('TRIM(UPPER(name)) = ?', [$groupNameUpper])
                        ->first();

                    $groups[$groupNameUpper] = $existingGroup ?: GeneralExamParticipantGroup::create([
                        'name' => $groupNameUpper,
                        'description' => "Imported from CSV on " . now()->format('Y-m-d'),
                        'parent_id' => null,
                    ]);
                }

                $parent = $groups[$groupNameUpper];

                // Create or reuse programme under the parent by normalized name.
                $programmeKey = $parent->id . '||' . $programmeNameUpper;
                if (!isset($programmes[$programmeKey])) {
                    $existing = GeneralExamParticipantGroup::where('parent_id', $parent->id)
                        ->whereRaw('TRIM(UPPER(name)) = ?', [$programmeNameUpper])
                        ->first();

                    $programmes[$programmeKey] = $existing ?: GeneralExamParticipantGroup::create([
                        'name' => $programmeNameUpper,
                        'description' => "Imported programme from CSV on " . now()->format('Y-m-d'),
                        'parent_id' => $parent->id,
                    ]);
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
