<?php

namespace App\Services;

use App\Models\Student;

class StudentUsernameService
{
    public function generate(Student $student): string
    {
        $year = $student->enrollment_year ?? now()->year;
        $seq = str_pad($student->id, 4, '0', STR_PAD_LEFT);

        return "STU-{$year}-{$seq}";
    }
}

