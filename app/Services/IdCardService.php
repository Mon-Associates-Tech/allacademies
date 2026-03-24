<?php

namespace App\Services;

use App\Models\IdCardTemplate;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentIdCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class IdCardService
{
    public function generateIdCard(Student $student, ?string $templateSlug = null): StudentIdCard
    {
        $school = $student->school;

        // Expire any existing active cards
        $student->idCards()->where('status', 'active')->update(['status' => 'expired']);

        // Determine template to use
        $templateName = $templateSlug ?? $school->id_card_template ?? 'professional';

        // Create new ID card
        $idCard = StudentIdCard::create([
            'student_id' => $student->id,
            'card_number' => $this->generateCardNumber($school),
            'issue_date' => now(),
            'expiry_date' => now()->addYear(),
            'barcode' => $this->generateBarcode($student),
            'status' => 'active',
            'template_used' => $templateName,
            'custom_data' => $this->buildCustomData($student, $school),
        ]);

        return $idCard;
    }

    public function regenerateIdCard(StudentIdCard $idCard): StudentIdCard
    {
        $student = $idCard->student;

        // Mark current card as replaced
        $idCard->update(['status' => 'replaced']);

        // Generate new card with same template
        return $this->generateIdCard($student, $idCard->template_used);
    }

    public function generatePdf(StudentIdCard $idCard): string
    {
        $student = $idCard->student;
        $school = $student->school;
        $templateName = $idCard->template_used ?? 'professional';

        $pdf = PDF::loadView(
            'components.id-cards.'.$templateName,
            [
                'student' => $student,
                'idCard' => $idCard,
                'school' => $school,
                'customFields' => $this->getCustomFields($school),
            ]
        );

        // ID card size (CR80 standard: 85.6mm x 53.98mm)
        $pdf->setPaper([0, 0, 242.65, 153.01], 'portrait');

        $filename = "id-cards/{$idCard->card_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    public function downloadPdf(StudentIdCard $idCard): \Illuminate\Http\Response
    {
        $student = $idCard->student;
        $school = $student->school;
        $templateName = $idCard->template_used ?? 'professional';

        $pdf = PDF::loadView(
            'components.id-cards.'.$templateName,
            [
                'student' => $student,
                'idCard' => $idCard,
                'school' => $school,
                'customFields' => $this->getCustomFields($school),
            ]
        );

        $pdf->setPaper([0, 0, 242.65, 153.01], 'portrait');

        return $pdf->download("id-card-{$student->user->name}.pdf");
    }

    public function expireCard(StudentIdCard $idCard): bool
    {
        return $idCard->update(['status' => 'expired']);
    }

    public function reportLost(StudentIdCard $idCard): bool
    {
        return $idCard->update(['status' => 'lost']);
    }

    public function getActiveCard(Student $student): ?StudentIdCard
    {
        return $student->idCards()
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->first();
    }

    public function hasValidCard(Student $student): bool
    {
        return $this->getActiveCard($student) !== null;
    }

    public function getAvailableTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return IdCardTemplate::where('is_active', true)->get();
    }

    public function getTemplate(string $slug): ?IdCardTemplate
    {
        return IdCardTemplate::where('slug', $slug)->first();
    }

    public function updateSchoolTemplate(School $school, string $templateSlug, array $customFields = []): bool
    {
        return $school->update([
            'id_card_template' => $templateSlug,
            'id_card_custom_fields' => $customFields,
        ]);
    }

    protected function generateCardNumber(School $school): string
    {
        $prefix = 'ID';
        $schoolCode = $school ? substr(strtoupper($school->code ?? $school->name), 0, 3) : 'GEN';
        $timestamp = now()->format('ymd');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$schoolCode}{$timestamp}{$random}";
    }

    protected function generateBarcode(Student $student): string
    {
        return $student->student_id.'-'.now()->format('Ymd');
    }

    protected function buildCustomData(Student $student, ?School $school): array
    {
        return [
            'generated_at' => now()->toIso8601String(),
            'school_name' => $school?->name,
            'school_address' => $school?->address,
            'school_phone' => $school?->phone,
            'student_name' => $student->user->name,
            'student_id' => $student->student_id,
            'academic_level' => $student->academicLevel?->name,
            'student_group' => $student->studentGroup?->name,
            'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
            'blood_group' => $student->blood_group,
            'emergency_contact' => $student->emergency_contact,
        ];
    }

    protected function getCustomFields(?School $school): array
    {
        if (! $school) {
            return [];
        }

        return $school->id_card_custom_fields ?? [];
    }

    public function previewTemplate(string $templateSlug, Student $student): string
    {
        $school = $student->school;

        // Create a temporary ID card for preview
        $previewCard = new StudentIdCard([
            'card_number' => 'PREVIEW-'.rand(1000, 9999),
            'issue_date' => now(),
            'expiry_date' => now()->addYear(),
            'barcode' => 'PREVIEW-BARCODE',
            'status' => 'active',
            'template_used' => $templateSlug,
        ]);

        return view('components.id-cards.'.$templateSlug, [
            'student' => $student,
            'idCard' => $previewCard,
            'school' => $school,
            'customFields' => $this->getCustomFields($school),
            'isPreview' => true,
        ])->render();
    }
}
