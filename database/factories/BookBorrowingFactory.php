<?php

namespace Database\Factories;

use App\Models\BookBorrowing;
use App\Models\Student;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookBorrowingFactory extends Factory
{
    protected $model = BookBorrowing::class;

    public function definition(): array
    {
        $borrowDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $dueDate = $this->faker->dateTimeBetween($borrowDate, '+1 month');
        $returnDate = $this->faker->optional(0.7)->dateTimeBetween($borrowDate, $dueDate);
        
        return [
            'student_id' => Student::factory(),
            'book_id' => Book::factory()->state(function (array $attributes) {
                return ['has_hardcopy' => true];
            }),
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'return_date' => $returnDate,
            'status' => $returnDate ? 'returned' : 'borrowed',
        ];
    }

    public function borrowed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'return_date' => null,
                'status' => 'borrowed',
            ];
        });
    }

    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'return_date' => $this->faker->dateTimeBetween($attributes['borrow_date'], $attributes['due_date']),
                'status' => 'returned',
            ];
        });
    }
}