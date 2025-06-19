<?php

namespace Database\Seeders;

use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use Database\Factories\AcademicLevelFactory;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Librarian;
use App\Models\Administrator;
use App\Models\Author;
use App\Models\BookCategory;
use App\Models\Book;
use App\Models\StudentGroup;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\GroupBookSubscription;
use App\Models\Assessment;
use App\Models\BookApproval;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator role with full access'],
            ['name' => 'student', 'description' => 'Student role with limited access'],
            ['name' => 'teacher', 'description' => 'Teacher role with access to educational content'],
            ['name' => 'librarian', 'description' => 'Librarian role with access to book management'],
            ['name' => 'author', 'description' => 'Author role with access to own publications']
        ];

        foreach ($roles as $role) {
          //  Role::create($role);
        }

        // Create admin user
//        $adminUser = User::create([
//            'name' => 'Admin User',
//            'email' => 'admin@example.com',
//            'password' => Hash::make('password'),
//            'role' => 'admin',
//            'email_verified_at' => now(),
//        ]);

       // $adminUser->roles()->attach(Role::where('name', 'admin')->first());

      //  Administrator::create(['user_id' => $adminUser->id]);

        // Create book categories
        $categories = [
            'Fiction', 'Non-Fiction', 'Science', 'Mathematics', 'History',
            'Geography', 'Biography', 'Autobiography', 'Fantasy', 'Mystery',
            'Thriller', 'Romance', 'Horror', 'Poetry', 'Drama',
            'Comics', 'Art', 'Cooking', 'Health', 'Travel'
        ];

        foreach ($categories as $category) {
//          //  BookCategory::create([
//                'name' => $category,
//                'description' => "Books in the {$category} category"
//          //  ]);
        }

        // Create subjects
        $subjects = [
            'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Computer Science',
            'English', 'Literature', 'History', 'Geography', 'Economics',
            'Art', 'Music', 'Physical Education', 'Psychology', 'Sociology',
            'Philosophy', 'Political Science', 'French', 'Spanish', 'German'
        ];

        foreach ($subjects as $subject) {
//            AcademicSubject::create([
//                'name' => $subject,
//                'academic_level_id' => AcademicLevelFactory::new()->create()->id,
//                'code' => strtoupper(substr($subject, 0, 3)),
//                'description' => "The study of {$subject}"
//            ]);
        }

        // Create teachers (20)
        $teachers = [];
        for ($i = 0; $i < 20; $i++) {
           // $user = User::factory()->create();
          //  $user->roles()->attach(Role::where('name', 'teacher')->first());
          //  $teachers[] = Teacher::create(['user_id' => $user->id]);
        }
            Log::info('Teachers created');
        // Create student groups (20)
        $studentGroups = [];
        $groupNames = [
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5',
            'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',
            'Grade 11', 'Grade 12', 'Class A', 'Class B', 'Class C',
            'Class D', 'Class E', 'Advanced Group', 'Beginners Group', 'Intermediate Group'
        ];

        foreach ($groupNames as $index => $name) {
          //  $studentGroups[] = StudentGroup::create([
         //       'name' => $name,
         //       'teacher_id' => $teachers[$index % count($teachers)]->id,
         //       'description' => "A group for {$name} students"
          //  ]);
        }
        Log::info('student groups created');;

        // Create students (20 per group)
        $allStudents = [];
        foreach ($studentGroups as $group) {
            for ($i = 0; $i < 20; $i++) {
              //  $user = User::factory()->create();
              //  $user->roles()->attach(Role::where('name', 'student')->first());
             //   $allStudents[] = Student::create([
             //       'user_id' => $user->id,
             //       'student_group_id' => $group->id
             //   ]);
            }
        }

        Log::info('students created');;

        // Create librarians (10)
        $librarians = [];
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory()->create();
            $user->roles()->attach(Role::where('name', 'librarian')->first());
            $librarians[] = Librarian::create(['user_id' => $user->id]);
        }

        Log::info('librarians created');;

        // Create authors (20)
        $authors = [];
        for ($i = 0; $i < 20; $i++) {
            $user = User::factory()->create();
            $user->roles()->attach(Role::where('name', 'author')->first());
            $authors[] = Author::create([
                'user_id' => $user->id,
//                'biography' => "Biography of author {$user->name}"
            ]);
        }

        Log::info('authors created');

        // Create books (5 per author)
        $allBooks = [];
        foreach ($authors as $author) {
            for ($i = 0; $i < 5; $i++) {
                $categoryId = BookCategory::inRandomOrder()->first()->id;
                $allBooks[] = Book::create([
                    'title' => "Book " . ($i + 1) . " by " . $author->user->name,
                    'author_id' => $author->id,
                    'book_category_id' => $categoryId,
                    'edition' => rand(1, 5) . "th Edition",
                    'publisher' => "Publisher " . rand(1, 10),
                    'pages' => rand(100, 800),
                    'has_hardcopy' => rand(0, 1),
                    'has_softcopy' => rand(0, 1) || $i % 2 == 0, // Ensure some books have softcopy
                    'additional_info' => "Additional information about the book"
                ]);
            }
        }

        Log::info('books created');;

        // Create topics (5 per subject)
        $allTopics = [];
        foreach (AcademicSubject::all() as $subject) {
            for ($i = 0; $i < 5; $i++) {
                $allTopics[] = AcademicTopic::create([
                    'name' => "Topic " . ($i + 1) . " in " . $subject->name,
                    'academic_subject_id' => $subject->id,
//                    'description' => "Description of topic " . ($i + 1) . " in " . $subject->name
                ]);
            }
        }

        Log::info('topics created');;

        // Create lessons (3 per teacher)
        $allLessons = [];
        foreach ($teachers as $teacher) {
            for ($i = 0; $i < 3; $i++) {
                $subject = AcademicSubject::inRandomOrder()->first();
                $group = StudentGroup::where('teacher_id', $teacher->id)->first() ?? StudentGroup::inRandomOrder()->first();

                $allLessons[] = Lesson::create([
                    'title' => "Lesson " . ($i + 1) . " by " . $teacher->user->name,
                    'description' => "Description of lesson " . ($i + 1),
                    'teacher_id' => $teacher->id,
                    'academic_subject_id' => $subject->id,
                    'student_group_id' => $group->id,
                    'date' => now()->addDays(rand(-30, 30))
                ]);
            }
        }

        Log::info('lessons created');

        // Create lesson notes (2 per lesson)
        foreach ($allLessons as $lesson) {
            for ($i = 0; $i < 2; $i++) {
                $topic = AcademicTopic::where('academic_subject_id', $lesson->subject_id)->inRandomOrder()->first() ?? AcademicTopic::inRandomOrder()->first();

                LessonNote::create([
                    'teacher_id' => $lesson->teacher_id,
                    'lesson_id' => $lesson->id,
                    'academic_subject_id' => $lesson->subject_id,
                    'academic_topic_id' => $topic->id,
                    'title' => "Note " . ($i + 1) . " for " . $lesson->title,
                    'content' => "Content of note " . ($i + 1) . " for " . $lesson->title,
                    'file_path' => null
                ]);
            }
        }

        Log::info('lesson notes created');

        // Create book borrowings (1 per 10 students)
        foreach ($allStudents as $index => $student) {
            if ($index % 10 == 0) {
                $hardcopyBooks = $allBooks->filter(function ($book) {
                    return $book->has_hardcopy;
                });

                if ($hardcopyBooks->isNotEmpty()) {
                    $book = $hardcopyBooks->random();
                    $borrowDate = now()->subDays(rand(1, 60));
                    $dueDate = (clone $borrowDate)->addDays(14);
                    $returnDate = rand(0, 1) ? (clone $borrowDate)->addDays(rand(1, 14)) : null;

                    BookBorrowing::create([
                        'student_id' => $student->id,
                        'book_id' => $book->id,
                        'borrow_date' => $borrowDate,
                        'due_date' => $dueDate,
                        'return_date' => $returnDate,
                        'status' => $returnDate ? 'returned' : 'borrowed'
                    ]);
                }
            }
        }

        Log::info('book borrowings created');;

        // Create book subscriptions (1 per 5 students)
        foreach ($allStudents as $index => $student) {
            if ($index % 5 == 0) {
                $softcopyBooks = $allBooks->filter(function ($book) {
                    return $book->has_softcopy;
                });

                if ($softcopyBooks->isNotEmpty()) {
                    $book = $softcopyBooks->random();
                    $startDate = now()->subDays(rand(1, 180));
                    $endDate = (clone $startDate)->addDays(rand(30, 365));

                    BookSubscription::create([
                        'student_id' => $student->id,
                        'book_id' => $book->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => now()->between($startDate, $endDate) ? 'active' : 'expired'
                    ]);
                }
            }
        }

        Log::info('book subscriptions created');
        // Create group book subscriptions (1 per group)
        foreach ($studentGroups as $group) {
            $softcopyBooks = $allBooks->filter(function ($book) {
                return $book->has_softcopy;
            });

            if ($softcopyBooks->isNotEmpty()) {
                $book = $softcopyBooks->random();
                $startDate = now()->subDays(rand(1, 90));
                $endDate = (clone $startDate)->addDays(rand(180, 365));

                GroupBookSubscription::create([
                    'student_group_id' => $group->id,
                    'book_id' => $book->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => now()->between($startDate, $endDate) ? 'active' : 'expired'
                ]);
            }
        }

        Log::info('group book subscriptions created');

        // Create assessments (1 per 3 students)
        foreach ($allStudents as $index => $student) {
            if ($index % 3 == 0) {
                $book = Book::inRandomOrder()->first();

                Assessment::create([
                    'student_id' => $student->id,
                    'book_id' => $book->id,
                    'score' => rand(0, 100),
                    'comments' => rand(0, 1) ? "Comments for assessment by student {$student->user->name}" : null
                ]);
            }
        }

        Log::info('assessments created');

        // Create book approvals (1 per 2 books)
        foreach ($allBooks as $index => $book) {
            if ($index % 2 == 0) {
                $librarian = $librarians[array_rand($librarians)];

                BookApproval::create([
                    'librarian_id' => $librarian->id,
                    'book_id' => $book->id,
//                    'approval_date' => now()->subDays(rand(1, 180)),
                    'status' => ['approved', 'rejected', 'pending'][rand(0, 2)],
                    'comments' => rand(0, 1) ? "Comments for book approval by librarian {$librarian->user->name}" : null
                ]);
            }
        }

        Log::info('book approvals created');
    }
}
