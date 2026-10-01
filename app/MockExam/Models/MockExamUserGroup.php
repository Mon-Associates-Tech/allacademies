<?php

namespace App\MockExam\Models;

enum MockExamUserGroup: string
{
    case Individual    = 'individual';
    case School        = 'school';
    case LocalGovt     = 'local_government';
    case Organization  = 'organization';

    public function label(): string
    {
        return match ($this) {
            self::Individual   => 'Individual',
            self::School       => 'School',
            self::LocalGovt    => 'Local Government',
            self::Organization => 'Organization / NGO',
        };
    }

    /** @return array<string, string> value => label */
    public static function options(): array
    {
        return array_column(
            array_map(fn ($case) => ['value' => $case->value, 'label' => $case->label()], self::cases()),
            'label',
            'value'
        );
    }
}
