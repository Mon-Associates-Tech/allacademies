<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all books and users
        $books = Book::all();
        $users = User::all();

        if ($books->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No books or users found. Please seed books and users first.');

            return;
        }

        $reviewTexts = [
            'This book is absolutely fantastic! The content is well-structured and easy to follow. I highly recommend it to anyone studying this subject.',
            'Good book overall, but some chapters could be explained better. The examples are helpful though.',
            'Excellent resource for students. The author has done a great job explaining complex concepts in simple terms.',
            'Not bad, but I expected more depth in certain topics. Still useful for basic understanding.',
            'Outstanding book! This has become my go-to reference for this subject. Very comprehensive and well-written.',
            'The book is okay but feels a bit outdated. Some information could be more current.',
            'Brilliant work! Clear explanations, great examples, and perfect for exam preparation.',
            'I found this book quite helpful for my studies. The layout and organization are very good.',
            'Disappointing. The content is too basic and doesn\'t cover advanced topics adequately.',
            'Superb book! Every student should have this. The explanations are crystal clear and the examples are relevant.',
        ];

        $reviewTitles = [
            'Excellent Resource!',
            'Very Helpful',
            'Great for Beginners',
            'Good but Not Perfect',
            'Highly Recommended',
            'Could Be Better',
            'Perfect for Exams',
            'Well Written',
            'Not What I Expected',
            'Amazing Book!',
        ];

        foreach ($books as $book) {
            // Generate 3-12 reviews per book
            $reviewCount = rand(3, 12);
            $usedUsers = [];

            for ($i = 0; $i < $reviewCount; $i++) {
                // Get a random user that hasn't reviewed this book yet
                $availableUsers = $users->whereNotIn('id', $usedUsers);
                if ($availableUsers->isEmpty()) {
                    break;
                }

                $user = $availableUsers->random();
                $usedUsers[] = $user->id;

                // Create review with weighted ratings (more 4-5 star reviews)
                $rating = $this->getWeightedRating();

                BookReview::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'reviewer_name' => $user->name,
                    'reviewer_email' => $user->email,
                    'rating' => $rating,
                    'title' => $reviewTitles[array_rand($reviewTitles)],
                    'review' => $reviewTexts[array_rand($reviewTexts)],
                    'is_verified_purchase' => rand(1, 100) <= 70, // 70% verified
                    'is_approved' => rand(1, 100) <= 95, // 95% approved
                    'helpful_votes' => [],
                    'helpful_count' => 0,
                    'approved_at' => now()->subDays(rand(1, 365)),
                    'created_at' => now()->subDays(rand(1, 365)),
                ]);
            }

            // Update book's average rating
            $book->updateAverageRating();
        }

        // Add some helpful votes to random reviews
        $reviews = BookReview::approved()->get();
        foreach ($reviews as $review) {
            if (rand(1, 100) <= 30) { // 30% chance of having helpful votes
                $helpfulCount = rand(1, 8);
                $randomUsers = $users->random($helpfulCount)->pluck('id')->toArray();

                $review->update([
                    'helpful_votes' => $randomUsers,
                    'helpful_count' => count($randomUsers),
                ]);
            }
        }

        // Update table of contents for some books
        foreach ($books->take(10) as $book) {
            $chaptersCount = rand(8, 15);
            $chapters = [];
            $currentPage = 1;

            for ($i = 1; $i <= $chaptersCount; $i++) {
                $chapterPages = rand(15, 35);
                $chapters[] = [
                    'chapter' => $i,
                    'title' => "Chapter {$i}: ".$this->getChapterTitle(),
                    'description' => $this->getChapterDescription(),
                    'page_start' => $currentPage,
                    'page_end' => $currentPage + $chapterPages - 1,
                    'sections' => $this->getChapterSections(),
                ];
                $currentPage += $chapterPages;
            }

            $book->update(['table_of_contents' => $chapters]);
        }

        $this->command->info('Book reviews and table of contents seeded successfully!');
    }

    /**
     * Get weighted rating (more 4-5 star ratings)
     */
    private function getWeightedRating(): int
    {
        $weights = [1 => 5, 2 => 10, 3 => 15, 4 => 35, 5 => 35];
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($weights as $rating => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $rating;
            }
        }

        return 5; // fallback
    }

    /**
     * Get random chapter title
     */
    private function getChapterTitle(): string
    {
        $titles = [
            'Introduction to Core Concepts',
            'Fundamental Principles',
            'Advanced Techniques',
            'Practical Applications',
            'Case Studies and Examples',
            'Problem Solving Methods',
            'Analysis and Evaluation',
            'Current Trends and Developments',
            'Future Perspectives',
            'Summary and Conclusions',
            'Key Theories Explained',
            'Implementation Strategies',
            'Best Practices Guide',
            'Common Challenges',
            'Solutions and Alternatives',
        ];

        return $titles[array_rand($titles)];
    }

    /**
     * Get random chapter description
     */
    private function getChapterDescription(): string
    {
        $descriptions = [
            'This chapter covers the essential concepts and foundational knowledge needed for understanding the subject.',
            'An in-depth exploration of key principles with practical examples and real-world applications.',
            'Advanced topics and techniques for students who want to deepen their understanding.',
            'Practical exercises and case studies to reinforce learning and build problem-solving skills.',
            'A comprehensive review of current research and developments in the field.',
            'Step-by-step guidance on implementing theoretical concepts in practical situations.',
            'Critical analysis of important concepts with detailed explanations and examples.',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get random chapter sections
     */
    private function getChapterSections(): array
    {
        $sectionCount = rand(2, 5);
        $sections = [];

        for ($i = 1; $i <= $sectionCount; $i++) {
            $sections[] = [
                'section' => $i,
                'title' => "Section {$i}",
                'page_count' => rand(3, 8),
            ];
        }

        return $sections;
    }
}
