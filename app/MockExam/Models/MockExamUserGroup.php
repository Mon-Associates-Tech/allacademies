<?php

namespace App\MockExam\Models;

enum MockExamUserGroup: string
{
    case Individual    = 'individual';
    case School        = 'school';
    case LocalGovt     = 'local_government';
    case Organization  = 'organization';
    case Other         = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Individual   => 'Individual',
            self::School       => 'School',
            self::LocalGovt    => 'District Assembly',
            self::Organization => 'Organization / NGO',
            self::Other        => 'Other',
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
