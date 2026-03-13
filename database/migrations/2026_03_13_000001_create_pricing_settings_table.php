<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->decimal('value', 10, 2);
            $table->timestamps();
        });

        $now = now();

        DB::table('pricing_settings')->insert([
            ['key' => 'basic.individual.quarter', 'value' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.individual.half', 'value' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.individual.year', 'value' => 45, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.individual.one_off', 'value' => 10, 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'senior.individual.quarter', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.individual.half', 'value' => 50, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.individual.year', 'value' => 75, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.individual.one_off', 'value' => 15, 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'university.individual.quarter', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.individual.half', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.individual.year', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.individual.one_off', 'value' => 20, 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'basic.institution.quarter', 'value' => 45, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.institution.half', 'value' => 75, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.institution.year', 'value' => 45, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.institution.mid_term', 'value' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'basic.institution.mock_exams', 'value' => 10, 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'senior.institution.quarter', 'value' => 75, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.institution.half', 'value' => 90, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.institution.year', 'value' => 75, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.institution.mid_term', 'value' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'senior.institution.mock_exams', 'value' => 20, 'created_at' => $now, 'updated_at' => $now],

            ['key' => 'university.institution.quarter', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.institution.half', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.institution.year', 'value' => 35, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.institution.mid_term', 'value' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'university.institution.mock_exams', 'value' => 0, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_settings');
    }
};
