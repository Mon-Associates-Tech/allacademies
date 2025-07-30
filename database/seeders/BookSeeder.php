<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Author;
use App\Models\BookCategory;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have authors, categories, and schools
        $this->ensureRequiredData();

        // Get existing data
        $authors = Author::all();
        $categories = BookCategory::all();
        $schools = School::all();

        // Sample books data
        $books = [
            // Mathematics Books
            [
                'title' => 'Advanced Mathematics for Engineering',
                'edition' => '5th Edition',
                'publisher' => 'Academic Press',
                'pages' => 450,
                'description' => 'Comprehensive mathematics textbook covering calculus, linear algebra, and differential equations for engineering students.',
                'publication_date' => '2023-01-15',
                'language' => 'English',
                'annual_subscription_fee' => 75.00,
                'is_free' => false,
                'price' => 150.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Mathematics',
            ],
            [
                'title' => 'Basic Arithmetic and Number Theory',
                'edition' => '2nd Edition',
                'publisher' => 'Educational Publishers',
                'pages' => 280,
                'description' => 'Foundation mathematics covering basic arithmetic, fractions, and introduction to number theory.',
                'publication_date' => '2022-08-20',
                'language' => 'English',
                'annual_subscription_fee' => 0.00,
                'is_free' => true,
                'price' => 0.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Mathematics',
            ],

            // Science Books
            [
                'title' => 'Introduction to Physics',
                'edition' => '8th Edition',
                'publisher' => 'Science Publications',
                'pages' => 620,
                'description' => 'Comprehensive introduction to physics covering mechanics, thermodynamics, and electromagnetism.',
                'publication_date' => '2023-03-10',
                'language' => 'English',
                'annual_subscription_fee' => 80.00,
                'is_free' => false,
                'price' => 180.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Science',
            ],
            [
                'title' => 'Chemistry for Beginners',
                'edition' => '4th Edition',
                'publisher' => 'Chemical Education Press',
                'pages' => 380,
                'description' => 'An accessible introduction to chemistry principles and laboratory practices.',
                'publication_date' => '2022-11-05',
                'language' => 'English',
                'annual_subscription_fee' => 60.00,
                'is_free' => false,
                'price' => 120.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Science',
            ],
            [
                'title' => 'Biology: Life Sciences',
                'edition' => '6th Edition',
                'publisher' => 'Life Science Books',
                'pages' => 550,
                'description' => 'Comprehensive biology textbook covering cell biology, genetics, and ecology.',
                'publication_date' => '2023-02-14',
                'language' => 'English',
                'annual_subscription_fee' => 70.00,
                'is_free' => false,
                'price' => 160.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Science',
            ],

            // Literature Books
            [
                'title' => 'World Literature Anthology',
                'edition' => '3rd Edition',
                'publisher' => 'Literary Press',
                'pages' => 700,
                'description' => 'Collection of classic and contemporary literature from around the world.',
                'publication_date' => '2022-09-18',
                'language' => 'English',
                'annual_subscription_fee' => 45.00,
                'is_free' => false,
                'price' => 90.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Literature',
            ],
            [
                'title' => 'Creative Writing Workshop',
                'edition' => '1st Edition',
                'publisher' => 'Writers Guild',
                'pages' => 320,
                'description' => 'A practical guide to creative writing with exercises and examples.',
                'publication_date' => '2023-05-22',
                'language' => 'English',
                'annual_subscription_fee' => 0.00,
                'is_free' => true,
                'price' => 0.00,
                'has_hardcopy' => false,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Literature',
            ],

            // History Books
            [
                'title' => 'African History: Ancient to Modern',
                'edition' => '2nd Edition',
                'publisher' => 'Historical Studies Press',
                'pages' => 480,
                'description' => 'Comprehensive overview of African history from ancient civilizations to contemporary times.',
                'publication_date' => '2022-12-08',
                'language' => 'English',
                'annual_subscription_fee' => 55.00,
                'is_free' => false,
                'price' => 110.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'History',
            ],
            [
                'title' => 'World War Chronicles',
                'edition' => '4th Edition',
                'publisher' => 'Military History Books',
                'pages' => 650,
                'description' => 'Detailed account of major world conflicts and their impact on global history.',
                'publication_date' => '2023-01-30',
                'language' => 'English',
                'annual_subscription_fee' => 65.00,
                'is_free' => false,
                'price' => 140.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'History',
            ],

            // Computer Science Books
            [
                'title' => 'Introduction to Programming',
                'edition' => '7th Edition',
                'publisher' => 'Tech Publications',
                'pages' => 520,
                'description' => 'Learn programming fundamentals using Python and Java with practical examples.',
                'publication_date' => '2023-04-12',
                'language' => 'English',
                'annual_subscription_fee' => 85.00,
                'is_free' => false,
                'price' => 190.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Computer Science',
            ],
            [
                'title' => 'Web Development Fundamentals',
                'edition' => '3rd Edition',
                'publisher' => 'Web Tech Press',
                'pages' => 420,
                'description' => 'Complete guide to web development covering HTML, CSS, JavaScript, and frameworks.',
                'publication_date' => '2023-06-01',
                'language' => 'English',
                'annual_subscription_fee' => 0.00,
                'is_free' => true,
                'price' => 0.00,
                'has_hardcopy' => false,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Computer Science',
            ],

            // Language Books
            [
                'title' => 'English Grammar and Composition',
                'edition' => '5th Edition',
                'publisher' => 'Language Learning Press',
                'pages' => 350,
                'description' => 'Comprehensive English grammar guide with exercises and composition techniques.',
                'publication_date' => '2022-10-15',
                'language' => 'English',
                'annual_subscription_fee' => 40.00,
                'is_free' => false,
                'price' => 80.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Language',
            ],
            [
                'title' => 'French for Beginners',
                'edition' => '2nd Edition',
                'publisher' => 'Language Academy',
                'pages' => 280,
                'description' => 'Interactive French language learning book with audio exercises.',
                'publication_date' => '2023-03-25',
                'language' => 'English/French',
                'annual_subscription_fee' => 50.00,
                'is_free' => false,
                'price' => 100.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Language',
            ],

            // Geography Books
            [
                'title' => 'Physical Geography Essentials',
                'edition' => '4th Edition',
                'publisher' => 'Geography Publications',
                'pages' => 410,
                'description' => 'Study of physical geography including climate, landforms, and natural processes.',
                'publication_date' => '2022-07-20',
                'language' => 'English',
                'annual_subscription_fee' => 58.00,
                'is_free' => false,
                'price' => 115.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Geography',
            ],

            // Reference Books
            [
                'title' => 'Academic Research Methods',
                'edition' => '6th Edition',
                'publisher' => 'Research Publications',
                'pages' => 390,
                'description' => 'Guide to academic research methodology and citation practices.',
                'publication_date' => '2023-02-28',
                'language' => 'English',
                'annual_subscription_fee' => 0.00,
                'is_free' => true,
                'price' => 0.00,
                'has_hardcopy' => true,
                'has_softcopy' => true,
                'status' => 'published',
                'category_name' => 'Reference',
            ]
        ];

        foreach ($books as $bookData) {
            // Find or create category
            $category = $categories->where('name', $bookData['category_name'])->first();
            if (!$category) {
                $category = BookCategory::create([
                    'name' => $bookData['category_name'],
                    'description' => 'Books related to ' . $bookData['category_name']
                ]);
                $categories->push($category);
            }

            // Get random author and school
            $author = $authors->random();
            $school = $schools->isNotEmpty() ? $schools->random() : null;

            // Remove category_name from book data
            unset($bookData['category_name']);

            Book::create(array_merge($bookData, [
                'author_id' => $author->id,
                'book_category_id' => $category->id,
                'school_id' => $school?->id,
                'additional_info' => $this->generateAdditionalInfo(),
                'subscription_conditions' => $this->generateSubscriptionConditions(),
                'cover_image' => null, // Will use default from model
                'content_url' => null, // Will use default from model
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    private function ensureRequiredData(): void
    {
        // Create default book categories if they don't exist
        $defaultCategories = [
            'Mathematics' => 'Books related to mathematics and numerical studies',
            'Science' => 'Books covering various scientific disciplines',
            'Literature' => 'Books on literature, poetry, and creative writing',
            'History' => 'Books covering historical events and periods',
            'Computer Science' => 'Books on programming, technology, and computer science',
            'Language' => 'Books for language learning and linguistics',
            'Geography' => 'Books on physical and human geography',
            'Reference' => 'Reference books and general academic resources',
        ];

        foreach ($defaultCategories as $name => $description) {
            BookCategory::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Create authors if none exist
        if (Author::count() === 0) {
            // Create some sample users for authors
            $authorUsers = [
                ['name' => 'Dr. John Smith', 'email' => 'john.smith@example.com'],
                ['name' => 'Prof. Sarah Johnson', 'email' => 'sarah.johnson@example.com'],
                ['name' => 'Dr. Michael Brown', 'email' => 'michael.brown@example.com'],
                ['name' => 'Prof. Emily Davis', 'email' => 'emily.davis@example.com'],
                ['name' => 'Dr. Robert Wilson', 'email' => 'robert.wilson@example.com'],
            ];

            foreach ($authorUsers as $userData) {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    array_merge($userData, [
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                        'role' => 'author',
                    ])
                );

                Author::firstOrCreate(['user_id' => $user->id]);
            }
        }

        // Create a default school if none exist
        if (School::count() === 0) {
            School::create([
                'name' => 'All Academies School',
                'address' => '123 Education Street, Learning City',
                'phone' => '+1234567890',
                'email' => 'info@allacademies.edu',
                'website' => 'https://allacademies.edu',
//                'status' => 'active',
            ]);
        }
    }

    private function generateAdditionalInfo(): string
    {
        $infos = [
            'Includes practice exercises and solutions.',
            'Features updated content reflecting current curriculum standards.',
            'Supplemented with online resources and video tutorials.',
            'Includes case studies and real-world applications.',
            'Written by leading experts in the field.',
            'Peer-reviewed and academically recognized.',
            'Contains extensive bibliography and further reading suggestions.',
            'Suitable for both classroom instruction and self-study.',
        ];

        return $infos[array_rand($infos)];
    }

    private function generateSubscriptionConditions(): string
    {
        return "1. Subscription is valid for one year from payment date\n" .
               "2. Book content is for reading only - no downloading, copying or printing allowed\n" .
               "3. Access will be revoked upon subscription expiry\n" .
               "4. Subscription is non-refundable\n" .
               "5. Content is protected by copyright laws\n" .
               "6. Access requires stable internet connection\n" .
               "7. Account sharing is strictly prohibited";
    }
}
