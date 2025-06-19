<?php

namespace Database\Factories;

use App\Models\BookApproval;
use App\Models\Librarian;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookApprovalFactory extends Factory
{
    protected $model = BookApproval::class;

    public function definition(): array
    {
        return [
            'librarian_id' => Librarian::factory(),
            'book_id' => Book::factory(),
            'approval_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['approved', 'rejected', 'pending']),
            'comments' => $this->faker->optional(0.7)->paragraph(),
        ];
    }
}