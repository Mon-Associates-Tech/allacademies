<?php

namespace Database\Factories;

use App\Models\Librarian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibrarianFactory extends Factory
{
    protected $model = Librarian::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
