<?php

namespace App\Livewire\Students;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\LibraryCard;
use App\Models\ReportCard;
use App\Models\ReportCardGrade;
use App\Models\ReportCardConfiguration;
use App\Models\Student;
use App\Models\StudentIdCard;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DocumentGenerator extends Component
{
    public Student $student;

    public $documentType = 'report-card';

    public $selectedAcademicYearId;

    public $selectedTerm = 'Term 1';

    public $academicYears;

    public $reportCards;

    public $idCards;

    public $libraryCards;

    public $attendanceSummary;

    // Report card data
    public $grades = [];

    public $subjectIds = [];

    public $availableSubjects = [];

    public $assignedSubjects = [];

    public $selectedAssignedSubjectIds = [];

    public $minSubjectLimit = null;

    public $maxSubjectLimit = null;

    // ID Card data
    public $cardExpiryMonths = 12;

    // Library Card data
    public $libraryCardType = 'student';

    public $libraryCardExpiryMonths = 12;

    // Preview mode
    public $previewMode = false;

    public $previewData = null;

    protected $queryString = ['documentType'];

    protected $rules = [
        'selectedAcademicYearId' => 'required|exists:academic_years,id',
        'selectedTerm' => 'required|string',
        // Weighting: Assignments 40%, Quizzes 10%, Final Exam 50%
        'grades.*.assessments_score' => 'nullable|numeric|min:0|max:40',
        'grades.*.quizzes_score' => 'nullable|numeric|min:0|max:10',
        'grades.*.final_exam_score' => 'nullable|numeric|min:0|max:50',
        'grades.*.remarks' => 'nullable|string|max:255',
        'selectedAssignedSubjectIds' => 'nullable|array',
        'selectedAssignedSubjectIds.*' => 'exists:academic_subjects,id',
    ];

    protected $messages = [
        'selectedAcademicYearId.required' => 'Please select an academic year.',
        'selectedTerm.required' => 'Please select a term.',
        'grades.*.assessments_score.max' => 'Assignments score cannot exceed 40.',
        'grades.*.quizzes_score.max' => 'Quizzes score cannot exceed 10.',
        'grades.*.final_exam_score.max' => 'Final exam score cannot exceed 50.',
    ];

    public function mount(Student $student)
    {
        $this->student = $student->load([
            'user',
            'academicLevel',
            'academicGroup',
            'school',
            'reportCards.grades.subject',
            'idCards',
            'libraryCards',
            'attendanceRecords',
        ]);

        // Load academic years
        $this->academicYears = AcademicYear::where('school_id', getSchoolId())
            ->orderBy('start_date', 'desc')
            ->get();

        // Set default academic year (current year)
        if ($this->academicYears->isNotEmpty()) {
            $currentYear = $this->academicYears->first();
            $this->selectedAcademicYearId = $currentYear->id;
        }

        $this->loadExistingData();
        $this->loadSubjectAssignmentData();
        $this->initializeGrades();
        $this->calculateAttendanceSummary();
    }

    public function updatedDocumentType()
    {
        $this->previewMode = false;
        $this->previewData = null;

        if ($this->documentType === 'report-card') {
            $this->initializeGrades();
        }
    }

    public function updatedSelectedAcademicYearId()
    {
        $this->loadExistingData();
        $this->loadSubjectAssignmentData();
        $this->initializeGrades();
    }

    public function updatedSelectedTerm()
    {
        $this->loadExistingData();
        $this->loadSubjectAssignmentData();
        $this->initializeGrades();
    }

    private function loadSubjectAssignmentData(): void
    {
        $levelSubjects = AcademicSubject::query()
            ->where('academic_level_id', $this->student->academic_level_id)
            ->orderBy('name')
            ->get();

        $assignedSubjects = $this->student->individualSubjects()
            ->wherePivot('is_active', true)
            ->orderBy('name')
            ->get();

        $this->availableSubjects = $levelSubjects
            ->merge($assignedSubjects)
            ->unique('id')
            ->values();

        $this->assignedSubjects = $assignedSubjects;
        $this->selectedAssignedSubjectIds = $assignedSubjects->pluck('id')->values()->all();

        $this->resolveSubjectLimits();
    }

    private function resolveSubjectLimits(): void
    {
        $configQuery = ReportCardConfiguration::query()
            ->where('school_id', getSchoolId())
            ->where('academic_level_id', $this->student->academic_level_id);

        if ($this->selectedAcademicYearId && $this->selectedTerm) {
            $configQuery->whereHas('academicPeriod', function ($query): void {
                $query->where('academic_year_id', $this->selectedAcademicYearId)
                    ->where('name', $this->selectedTerm);
            });
        }

        $config = $configQuery
            ->latest('id')
            ->first();

        $this->minSubjectLimit = $config?->min_subjects;
        $this->maxSubjectLimit = $config?->max_subjects;
    }

    private function loadExistingData()
    {
        $this->reportCards = $this->student->reportCards()
            ->with(['grades.subject', 'academicYear'])
            ->orderBy('generated_at', 'desc')
            ->get();

        $this->idCards = $this->student->idCards()
            ->orderBy('issue_date', 'desc')
            ->get();

        $this->libraryCards = $this->student->libraryCards()
            ->orderBy('issued_date', 'desc')
            ->get();
    }

    private function initializeGrades()
    {
        // Report cards are restricted to explicitly assigned subjects only.
        $subjects = collect($this->assignedSubjects);

        // Check if there's an existing report card
        $existingReportCard = ReportCard::where([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->selectedAcademicYearId,
            'term' => $this->selectedTerm,
        ])->first();

        $this->grades = [];
        $this->subjectIds = [];

        foreach ($subjects as $subject) {
            $this->subjectIds[] = $subject->id;

            // Load existing grades if available
            $existingGrade = $existingReportCard
                ? $existingReportCard->grades()->where('subject_id', $subject->id)->first()
                : null;

            // Auto-compute assignments (assessments) score from submissions (40% max)
            $computedAssignmentsScore = $this->computeAssignmentsScoreForSubject($subject->id);

            $assessments = $existingGrade->assessments_score ?? $computedAssignmentsScore;
            $quizzes = $existingGrade->quizzes_score ?? 0;
            $finalExam = $existingGrade->final_exam_score ?? 0;
            $total = ($assessments ?: 0) + ($quizzes ?: 0) + ($finalExam ?: 0);

            $this->grades[$subject->id] = [
                'subject_name' => $subject->name,
                'assessments_score' => $assessments,
                'quizzes_score' => $quizzes,
                'final_exam_score' => $finalExam,
                'total_score' => $existingGrade->total_score ?? $total,
                'grade_label' => $existingGrade->grade_label ?? $this->calculateGradeLabel($total),
                'remarks' => $existingGrade->remarks ?? '',
            ];
        }
    }

    public function saveAssignedSubjects(): void
    {
        $this->validate([
            'selectedAssignedSubjectIds' => 'nullable|array',
            'selectedAssignedSubjectIds.*' => 'exists:academic_subjects,id',
        ]);

        $selectedCount = count($this->selectedAssignedSubjectIds);

        if ($this->minSubjectLimit !== null && $selectedCount < $this->minSubjectLimit) {
            session()->flash('error', "At least {$this->minSubjectLimit} subjects must be assigned for this level.");

            return;
        }

        if ($this->maxSubjectLimit !== null && $selectedCount > $this->maxSubjectLimit) {
            session()->flash('error', "At most {$this->maxSubjectLimit} subjects can be assigned for this level.");

            return;
        }

        DB::transaction(function (): void {
            $currentActive = $this->student->individualSubjects()
                ->wherePivot('is_active', true)
                ->pluck('academic_subjects.id')
                ->all();

            foreach ($this->selectedAssignedSubjectIds as $subjectId) {
                $exists = DB::table('student_subject')
                    ->where('student_id', $this->student->id)
                    ->where('academic_subject_id', $subjectId)
                    ->exists();

                if ($exists) {
                    DB::table('student_subject')
                        ->where('student_id', $this->student->id)
                        ->where('academic_subject_id', $subjectId)
                        ->update([
                            'is_active' => true,
                            'assigned_by' => Auth::id(),
                            'notes' => 'Assigned for report card',
                            'assigned_at' => now(),
                            'updated_at' => now(),
                        ]);
                } else {
                    $this->student->individualSubjects()->attach($subjectId, [
                        'is_active' => true,
                        'assigned_by' => Auth::id(),
                        'notes' => 'Assigned for report card',
                        'assigned_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $toDeactivate = array_diff($currentActive, $this->selectedAssignedSubjectIds);
            if (! empty($toDeactivate)) {
                DB::table('student_subject')
                    ->where('student_id', $this->student->id)
                    ->whereIn('academic_subject_id', $toDeactivate)
                    ->update([
                        'is_active' => false,
                        'assigned_by' => Auth::id(),
                        'notes' => 'Removed from report card subject assignment',
                        'assigned_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        });

        $this->student->refresh();
        $this->loadSubjectAssignmentData();
        $this->initializeGrades();

        session()->flash('success', 'Assigned subjects updated successfully.');
    }

    public function updatedGrades($value, $key)
    {
        // Extract subject ID from the key (e.g., "123.assessments_score" -> "123")
        $parts = explode('.', $key);
        $subjectId = $parts[0];

        if (isset($this->grades[$subjectId])) {
            // Calculate total score
            $assessments = (float) ($this->grades[$subjectId]['assessments_score'] ?? 0);
            $quizzes = (float) ($this->grades[$subjectId]['quizzes_score'] ?? 0);
            $finalExam = (float) ($this->grades[$subjectId]['final_exam_score'] ?? 0);

            $totalScore = $assessments + $quizzes + $finalExam;
            $this->grades[$subjectId]['total_score'] = $totalScore;
            $this->grades[$subjectId]['grade_label'] = $this->calculateGradeLabel($totalScore);
        }
    }

    private function calculateGradeLabel($score)
    {
        if ($score >= 90) {
            return 'A+';
        }
        if ($score >= 80) {
            return 'A';
        }
        if ($score >= 70) {
            return 'B+';
        }
        if ($score >= 60) {
            return 'B';
        }
        if ($score >= 50) {
            return 'C';
        }
        if ($score >= 40) {
            return 'D';
        }

        return 'F';
    }

    /**
     * Compute the student's Assignments aggregate for a subject within the selected academic year.
     * Result is scaled to the assignment weight (40 points max).
     */
    private function computeAssignmentsScoreForSubject($subjectId): float
    {
        if (!$this->selectedAcademicYearId) {
            return 0.0;
        }

        $academicYear = AcademicYear::find($this->selectedAcademicYearId);
        if (!$academicYear) {
            return 0.0;
        }

        // Sum scores from graded submissions for this subject within the academic year window
        $submissions = $this->student->assignmentSubmissions()
            ->where('status', 'graded')
            ->whereHas('assignment', function ($q) use ($subjectId) {
                $q->where('academic_subject_id', $subjectId);
            })
            ->whereBetween('submitted_at', [
                $academicYear->start_date,
                $academicYear->end_date,
            ])
            ->with('assignment:id,academic_subject_id')
            ->get();

        $totalObtained = (float) $submissions->sum(function ($s) {
            return (float) ($s->score ?? 0);
        });
        $totalPossible = (float) $submissions->sum(function ($s) {
            return (float) ($s->total_marks ?? 0);
        });

        if ($totalPossible <= 0) {
            return 0.0;
        }

        $percentage = ($totalObtained / $totalPossible) * 100.0; // 0-100
        // Scale to 40 points (assignments weight)
        $scaled = ($percentage / 100.0) * 40.0;

        // Round to 1 decimal place for neatness
        return round($scaled, 1);
    }

    private function calculateAttendanceSummary()
    {
        $records = $this->student->attendanceRecords()
            ->with('attendance')
            ->whereHas('attendance', function ($query) {
                if ($this->selectedAcademicYearId) {
                    $academicYear = AcademicYear::find($this->selectedAcademicYearId);
                    if ($academicYear) {
                        $query->whereBetween('date', [
                            $academicYear->start_date,
                            $academicYear->end_date,
                        ]);
                    }
                }
            })
            ->get();

        $totalSessions = $records->count();
        $presentCount = $records->where('status', 'present')->count();
        $absentCount = $records->where('status', 'absent')->count();
        $lateCount = $records->where('status', 'late')->count();

        $this->attendanceSummary = [
            'total' => $totalSessions,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'rate' => $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 0,
        ];
    }

    public function preview()
    {
        $this->previewMode = true;

        switch ($this->documentType) {
            case 'report-card':
                $this->previewData = $this->getReportCardData();
                break;
            case 'id-card':
                $this->previewData = $this->getIdCardData();
                break;
            case 'library-card':
                $this->previewData = $this->getLibraryCardData();
                break;
            case 'attendance-report':
                $this->previewData = $this->getAttendanceReportData();
                break;
        }
    }

    public function generateReportCard()
    {
        $this->validate();

        try {
            if (empty($this->selectedAssignedSubjectIds)) {
                session()->flash('error', 'No subjects assigned. Assign at least one subject before generating a report card.');

                return null;
            }

            if ($this->minSubjectLimit !== null && count($this->selectedAssignedSubjectIds) < $this->minSubjectLimit) {
                session()->flash('error', "This level requires at least {$this->minSubjectLimit} assigned subjects.");

                return null;
            }

            if ($this->maxSubjectLimit !== null && count($this->selectedAssignedSubjectIds) > $this->maxSubjectLimit) {
                session()->flash('error', "This level allows a maximum of {$this->maxSubjectLimit} assigned subjects.");

                return null;
            }

            // Resolve a teacher to attribute grades to (required by DB schema)
            $teacherId = $this->student->primary_teacher?->id;
            if (!$teacherId) {
                // Fallback: any assigned teacher
                $fallbackTeacher = $this->student->teachers()->first();
                $teacherId = $fallbackTeacher?->id;
            }

            if (!$teacherId) {
                session()->flash('error', 'Failed to generate report card: No teacher assigned to this student. Please assign a class/primary teacher first.');
                return null;
            }

            // Create or update report card
            $reportCard = ReportCard::updateOrCreate([
                'student_id' => $this->student->id,
                'academic_year_id' => $this->selectedAcademicYearId,
                'term' => $this->selectedTerm,
            ], [
                'school_id' => getSchoolId(),
                'generated_at' => now(),
            ]);

            // Save grades (ensure teacher_id is set to avoid DB errors)
            foreach ($this->grades as $subjectId => $gradeData) {
                $grade = ReportCardGrade::firstOrNew([
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $subjectId,
                ]);

                if (empty($grade->teacher_id)) {
                    $grade->teacher_id = $teacherId;
                }

                $grade->assessments_score = $gradeData['assessments_score'] ?: 0;
                $grade->quizzes_score = $gradeData['quizzes_score'] ?: 0;
                $grade->final_exam_score = $gradeData['final_exam_score'] ?: 0;
                $grade->total_score = $gradeData['total_score'];
                $grade->grade_label = $gradeData['grade_label'];
                $grade->remarks = $gradeData['remarks'];

                $grade->save();
            }

            $this->loadExistingData();

            session()->flash('success', 'Report card generated successfully!');

            return $reportCard;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate report card: '.$e->getMessage());

            return null;
        }
    }

    public function downloadReportCard($reportCardId = null)
    {
        if ($reportCardId) {
            $reportCard = ReportCard::findOrFail($reportCardId);
        } else {
            $reportCard = $this->generateReportCard();
            if (! $reportCard) {
                return;
            }
        }

        $reportCard->load(['student.user', 'student.academicLevel', 'student.primaryTeacher.user', 'student.studentGroup', 'grades.subject', 'academicYear', 'school']);

        // Prefer the report card's school, but fallback to the student's school for older records
        $school = $reportCard->school ?: $reportCard->student->school;

        $pdf = PDF::loadView('students.report-card-pdf', [
            'reportCard' => $reportCard,
            'attendanceSummary' => $this->attendanceSummary,
            'school' => $school,
            // Provide a defensive fallback for subjects in case an older report card lacks persisted grades
            'gradesArray' => $this->grades,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "report-card-{$this->student->user->name}-{$reportCard->term}.pdf");
    }

    public function generateIdCard()
    {
        try {
            // Check for existing active card
            $existingActiveCard = $this->student->idCards()
                ->where('status', 'active')
                ->where('expiry_date', '>', now())
                ->first();

            if ($existingActiveCard) {
                session()->flash('info', 'Student already has an active ID card. Generating a new one will expire the existing card.');
            }

            // Expire any existing active cards
            $this->student->idCards()->where('status', 'active')->update(['status' => 'expired']);

            // Generate new ID card
            $idCard = StudentIdCard::create([
                'student_id' => $this->student->id,
                'card_number' => $this->generateCardNumber('ID'),
                'issue_date' => now(),
                'expiry_date' => now()->addMonths($this->cardExpiryMonths),
                'status' => 'active',
                'barcode' => $this->generateBarcode(),
            ]);

            $this->loadExistingData();

            session()->flash('success', 'ID card generated successfully!');

            return $idCard;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate ID card: '.$e->getMessage());

            return null;
        }
    }

    public function downloadIdCard($idCardId = null)
    {
        if ($idCardId) {
            $idCard = StudentIdCard::findOrFail($idCardId);
        } else {
            $idCard = $this->generateIdCard();
            if (! $idCard) {
                return;
            }
        }

        $idCard->load('student.user', 'student.academicLevel', 'student.studentGroup', 'student.school');

        $pdf = PDF::loadView('students.id-card-pdf', [
            'student' => $idCard->student,
            'idCard' => $idCard,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "id-card-{$this->student->user->name}.pdf");
    }

    public function generateLibraryCard()
    {
        try {
            // Check for existing active card
            $existingActiveCard = $this->student->libraryCards()
                ->where('status', LibraryCard::STATUS_ACTIVE)
                ->where('expiry_date', '>', now())
                ->first();

            if ($existingActiveCard) {
                session()->flash('info', 'Student already has an active library card. Generating a new one will expire the existing card.');
            }

            // Expire any existing active cards
            $this->student->libraryCards()
                ->where('status', LibraryCard::STATUS_ACTIVE)
                ->update(['status' => LibraryCard::STATUS_EXPIRED]);

            // Generate new library card
            $libraryCard = LibraryCard::create([
                'student_id' => $this->student->id,
                'card_number' => $this->generateCardNumber('LIB'),
                'card_type' => $this->libraryCardType,
                'status' => LibraryCard::STATUS_ACTIVE,
                'issued_date' => now(),
                'expiry_date' => now()->addMonths($this->libraryCardExpiryMonths),
                'issued_by' => Auth::id(),
                'barcode' => $this->generateBarcode(),
            ]);

            $this->loadExistingData();

            session()->flash('success', 'Library card generated successfully!');

            return $libraryCard;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate library card: '.$e->getMessage());

            return null;
        }
    }

    public function downloadLibraryCard($libraryCardId = null)
    {
        if ($libraryCardId) {
            $libraryCard = LibraryCard::findOrFail($libraryCardId);
        } else {
            $libraryCard = $this->generateLibraryCard();
            if (! $libraryCard) {
                return;
            }
        }

        $libraryCard->load('student.user', 'student.academicLevel', 'student.school');

        $pdf = PDF::loadView('students.library-card', [
            'student' => $libraryCard->student,
            'libraryCard' => $libraryCard,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "library-card-{$this->student->user->name}.pdf");
    }

    public function downloadAttendanceReport()
    {
        $data = $this->getAttendanceReportData();

        $pdf = PDF::loadView('students.attendance-report', [
            'student' => $this->student,
            'data' => $data,
            'academicYear' => AcademicYear::find($this->selectedAcademicYearId),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "attendance-report-{$this->student->user->name}.pdf");
    }

    private function generateCardNumber($prefix)
    {
        return $prefix.$this->student->school?->code.date('Y').str_pad($this->student->id, 5, '0', STR_PAD_LEFT);
    }

    private function generateBarcode()
    {
        return 'BAR'.time().rand(1000, 9999);
    }

    private function getReportCardData()
    {
        return [
            'student' => $this->student,
            'school' => $this->student->school,
            'letterhead_template' => $this->student->school->letterhead_template ?? 'classic',
            'academic_year' => AcademicYear::find($this->selectedAcademicYearId),
            'term' => $this->selectedTerm,
            'grades' => $this->grades,
            'attendance' => $this->attendanceSummary,
        ];
    }

    private function getIdCardData()
    {
        return [
            'student' => $this->student,
            'school' => $this->student->school,
            'letterhead_template' => $this->student->school->letterhead_template ?? 'classic',
            'card_number' => $this->generateCardNumber('ID'),
            'issue_date' => now(),
            'expiry_date' => now()->addMonths($this->cardExpiryMonths),
        ];
    }

    private function getLibraryCardData()
    {
        return [
            'student' => $this->student,
            'school' => $this->student->school,
            'letterhead_template' => $this->student->school->letterhead_template ?? 'classic',
            'card_number' => $this->generateCardNumber('LIB'),
            'card_type' => $this->libraryCardType,
            'issued_date' => now(),
            'expiry_date' => now()->addMonths($this->libraryCardExpiryMonths),
        ];
    }

    private function getAttendanceReportData()
    {
        return [
            'school' => $this->student->school,
            'letterhead_template' => $this->student->school->letterhead_template ?? 'classic',
            'summary' => $this->attendanceSummary,
            'records' => $this->student->attendanceRecords()
                ->with(['attendance.academicLevel', 'attendance.academicSubject'])
                ->whereHas('attendance', function ($query) {
                    if ($this->selectedAcademicYearId) {
                        $academicYear = AcademicYear::find($this->selectedAcademicYearId);
                        if ($academicYear) {
                            $query->whereBetween('date', [
                                $academicYear->start_date,
                                $academicYear->end_date,
                            ]);
                        }
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get(),
        ];
    }

    public function render()
    {
        return view('livewire.students.document-generator');
    }
}
