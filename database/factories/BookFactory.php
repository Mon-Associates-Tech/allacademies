<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use App\Models\BookCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'author_id' => Author::factory(),
            'book_category_id' => BookCategory::factory(),
            'edition' => $this->faker->randomElement(['1st Edition', '2nd Edition', '3rd Edition', null]),
            'publisher' => $this->faker->company(),
            'pages' => $this->faker->numberBetween(50, 1000),
            'has_hardcopy' => $this->faker->boolean(70),
            'has_softcopy' => $this->faker->boolean(80),
            'additional_info' => $this->faker->paragraph(),
        ];
    }
}