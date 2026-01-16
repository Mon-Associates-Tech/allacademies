<?php

namespace App\Livewire;

use App\Models\FinancialAid;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FinancialAidManager extends Component
{
    use withFileUploads, WithPagination;

    public $search = '';

    public $isModalOpen = false;

    public $manageBeneficiariesMode = false;

    // Form Fields
    public $aidId;

    public $name;

    public $code;

    public $amount;

    public $description;

    public $status = 'active';

    // Structure ID
    public $school_payment_structure_id;

    // Options
    public $availableStructures = [];

    public $availableStudentGroups = [];

    // Beneficiary Management
    public $beneficiaryInput = '';

    public $beneficiaryFile; // 4. File property

    public $selectedGroupId; // 5. Group selection

    public $currentAid;

    protected $rules = [
        'name' => 'required|string|min:3',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|in:active,inactive',
        'school_payment_structure_id' => 'nullable|integer|exists:school_payment_structures,id',
    ];

    public function render()
    {
        $schoolId = $this->getSchoolId();

        $aids = FinancialAid::where('school_id', $schoolId)
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->withCount('beneficiaries')
            ->with('schoolPaymentStructure')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.financial-aid-manager', [
            'aids' => $aids,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->loadStructureOptions();
        $this->openModal();
    }

    public function edit($id)
    {
        $aid = FinancialAid::where('school_id', $this->getSchoolId())->findOrFail($id);

        $this->aidId = $id;
        $this->name = $aid->name;
        $this->code = $aid->code;
        $this->amount = $aid->amount;
        $this->description = $aid->description;
        $this->status = $aid->status;
        $this->school_payment_structure_id = $aid->school_payment_structure_id;

        $this->loadStructureOptions();
        $this->openModal();
    }

    public function loadStructureOptions()
    {
        $schoolId = $this->getSchoolId();

        // Load active structures (Tuition, Transport, etc.)
        $this->availableStructures = SchoolPaymentStructure::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($structure) {
                $type = ucwords(str_replace('_', ' ', $structure->payment_type));

                return [
                    'id' => $structure->id,
                    'label' => "{$structure->name} - {$type} (GHS ".number_format($structure->amount, 2).')',
                ];
            });
    }

    public function store()
    {
        $schoolId = $this->getSchoolId();

        if (! $schoolId) {
            session()->flash('error', 'School context not found.');

            return;
        }

        $rules = $this->rules;
        // Unique code check within school
        $rules['code'] = 'nullable|string|unique:financial_aids,code,'.($this->aidId ?? 'NULL').',id,school_id,'.$schoolId;

        $this->validate($rules);

        $code = $this->code ?: strtoupper('AID-'.Str::random(6));

        FinancialAid::updateOrCreate(['id' => $this->aidId], [
            'school_id' => $schoolId,
            'name' => $this->name,
            'code' => $code,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => $this->status,
            'school_payment_structure_id' => $this->school_payment_structure_id ?: null,
        ]);

        session()->flash('message', $this->aidId ? 'Financial Aid Updated.' : 'Financial Aid Created.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function addBeneficiaries()
    {
        $this->validate(['beneficiaryInput' => 'required|string']);
        $schoolId = $this->getSchoolId();

        $identifiers = preg_split('/[\s,]+/', $this->beneficiaryInput, -1, PREG_SPLIT_NO_EMPTY);

        $foundCount = 0;
        $notFound = [];

        foreach ($identifiers as $id) {
            $id = trim($id);

            $student = Student::where('school_id', $schoolId)->where('student_id', $id)->first();

            if (! $student) {
                $user = User::where('email', $id)
                    ->where(function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId)
                            ->orWhereHas('student', fn ($sq) => $sq->where('school_id', $schoolId));
                    })->first();

                if ($user) {
                    $student = Student::where('user_id', $user->id)->where('school_id', $schoolId)->first();
                }
            }

            if ($student) {
                if (! $this->currentAid->beneficiaries()->where('student_id', $student->id)->exists()) {
                    $this->currentAid->beneficiaries()->attach($student->id);
                    $foundCount++;
                }
            } else {
                $notFound[] = $id;
            }
        }

        $this->beneficiaryInput = '';
        $this->currentAid->refresh();
        session()->flash('message', "Added $foundCount students.".(count($notFound) ? ' Not found: '.implode(', ', $notFound) : ''));
    }

    public function removeBeneficiary($studentId)
    {
        if ($this->currentAid) {
            $this->currentAid->beneficiaries()->detach($studentId);
            $this->currentAid->refresh();
        }
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->manageBeneficiariesMode = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->manageBeneficiariesMode = false;
        $this->resetInputFields();
    }

    public function openBeneficiaries($id)
    {
        $this->currentAid = FinancialAid::where('school_id', $this->getSchoolId())
            ->with('beneficiaries.user')
            ->findOrFail($id);

        $this->loadStudentGroups(); // Load groups when opening modal

        $this->manageBeneficiariesMode = true;
        $this->isModalOpen = true;
    }

    public function loadStudentGroups()
    {
        // Assuming StudentGroup has a relationship to School or is filtered by context
        // Since StudentGroup model provided doesn't explicitly have school_id in fillable,
        // we might need to rely on the teacher's school or if you added school_id to it.
        // For now, assuming we can filter by current user's school context if applicable,
        // or if StudentGroup is linked to school.

        $schoolId = $this->getSchoolId();

        // Adjust query based on your actual StudentGroup schema.
        // If StudentGroup doesn't have school_id, you might need to filter by teachers in the school.
        $this->availableStudentGroups = StudentGroup::whereHas('students', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->get();
    }

    public function addBeneficiariesFromInput()
    {
        $this->validate(['beneficiaryInput' => 'required|string']);
        $identifiers = preg_split('/[\s,]+/', $this->beneficiaryInput, -1, PREG_SPLIT_NO_EMPTY);
        $this->processIdentifiers($identifiers);
        $this->beneficiaryInput = '';
    }

    public function addBeneficiariesFromGroup()
    {
        $this->validate(['selectedGroupId' => 'required|exists:student_groups,id']);

        $group = StudentGroup::with('students')->find($this->selectedGroupId);

        if (! $group) {
            return;
        }

        $count = 0;
        foreach ($group->students as $student) {
            // Ensure student belongs to current school context
            if ($student->school_id == $this->getSchoolId()) {
                if (! $this->currentAid->beneficiaries()->where('student_id', $student->id)->exists()) {
                    $this->currentAid->beneficiaries()->attach($student->id);
                    $count++;
                }
            }
        }

        $this->currentAid->refresh();
        $this->selectedGroupId = null;
        session()->flash('message', "Added $count students from group: {$group->name}");
    }

    public function addBeneficiariesFromFile()
    {
        $this->validate([
            'beneficiaryFile' => 'required|file|mimes:csv,txt|max:1024', // Max 1MB
        ]);

        $path = $this->beneficiaryFile->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $identifiers = [];

        // Assume simple list or check for headers
        foreach ($data as $row) {
            // If row has data, take the first column as ID/Email
            if (isset($row[0])) {
                $identifiers[] = trim($row[0]);
            }
        }

        // Filter out potential headers if "email" or "id" is in the first row
        $identifiers = array_filter($identifiers, function ($value) {
            return strtolower($value) !== 'email' && strtolower($value) !== 'student_id' && strtolower($value) !== 'id';
        });

        $this->processIdentifiers($identifiers);

        $this->beneficiaryFile = null; // Reset file input
    }

    private function processIdentifiers(array $identifiers)
    {
        $schoolId = $this->getSchoolId();
        $foundCount = 0;
        $notFound = [];

        foreach ($identifiers as $id) {
            $id = trim($id);
            if (empty($id)) {
                continue;
            }

            // 1. Try finding by School-Specific Student ID
            $student = Student::where('school_id', $schoolId)
                ->where('student_id', $id) // This refers to the string column 'student_id' in students table
                ->first();

            // 2. If not found, try by User Email
            if (! $student) {
                $user = User::where('email', $id)
                    ->where(function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId)
                            ->orWhereHas('student', fn ($sq) => $sq->where('school_id', $schoolId));
                    })->first();

                if ($user) {
                    $student = Student::where('user_id', $user->id)
                        ->where('school_id', $schoolId)
                        ->first();
                }
            }

            if ($student) {
                // FIX: Use qualified column name here to avoid ambiguity
                $exists = $this->currentAid->beneficiaries()
                    ->where('financial_aid_student.student_id', $student->id) // Refers to FK in pivot
                    ->exists();

                if (! $exists) {
                    $this->currentAid->beneficiaries()->attach($student->id);
                    $foundCount++;
                }
            } else {
                $notFound[] = $id;
            }
        }

        $this->currentAid->refresh();

        $msg = "Successfully added $foundCount students.";
        if (count($notFound) > 0) {
            $limit = 5;
            $missingStr = implode(', ', array_slice($notFound, 0, $limit));
            if (count($notFound) > $limit) {
                $missingStr .= '...';
            }
            $msg .= " Could not find: $missingStr";
        }

        session()->flash('message', $msg);
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->code = '';
        $this->amount = '';
        $this->description = '';
        $this->status = 'active';
        $this->school_payment_structure_id = null;
        $this->aidId = null;
        $this->currentAid = null;
        $this->beneficiaryInput = '';
        $this->beneficiaryFile = null;
        $this->selectedGroupId = null;
    }

    protected function getSchoolId(): ?int
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }
        if ($user->canAccessCrossSchool()) {
            return session('current_school_id') ?? (app()->bound('current_school') ? app('current_school')->id : null);
        }

        return $user->school_id;
    }
}
