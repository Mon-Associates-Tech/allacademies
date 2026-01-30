<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create quiz_sessions table (compatible with existing Book model)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->integer('chapter_id')->nullable(); // Using chapter number from table_of_contents
            $table->integer('page_start')->nullable();
            $table->integer('page_end')->nullable();
            $table->enum('question_type', ['essay', 'multiple_choice', 'true_false', 'mixed']);
            $table->integer('question_count');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->json('questions'); // generated questions
            $table->json('answers')->nullable(); // user answers
            $table->json('results')->nullable(); // grading results
            $table->json('context')->nullable(); // reading context
            $table->integer('time_taken')->nullable(); // seconds
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'book_id']);
            $table->index(['user_id', 'status']);
            $table->index('completed_at');
        });

        Schema::create('reading_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_type', 100); // books_read, quiz_score, reading_streak, etc.
            $table->string('achievement_name');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->json('criteria_met')->nullable(); // what criteria were met
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->index(['user_id', 'achievement_type']);
            $table->index(['user_id', 'earned_at']);
        });

        Schema::table('book_reading_progress', function (Blueprint $table) {
            $table->decimal('comprehension_score', 5, 2)->nullable()->after('last_read_at');
            $table->json('quiz_history')->nullable()->after('comprehension_score');
            $table->integer('reading_streak_days')->default(0)->after('quiz_history');
            $table->json('learning_notes')->nullable()->after('reading_streak_days');
        });

        Schema::create('book_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->integer('chapter_number')->nullable(); // Reference to table_of_contents
            $table->integer('page_number')->nullable();
            $table->enum('discussion_type', ['question', 'observation', 'theory', 'review'])->default('question');
            $table->json('tags')->nullable();
            $table->boolean('contains_spoilers')->default(false);
            $table->integer('likes_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['book_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['discussion_type', 'is_featured']);
        });

        Schema::create('book_discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_discussion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_reply_id')->nullable()->constrained('book_discussion_replies')->onDelete('cascade');
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->boolean('is_helpful')->default(false);
            $table->timestamps();

            $table->index(['book_discussion_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('vocabulary_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('word');
            $table->text('definition')->nullable();
            $table->text('context_sentence')->nullable(); // Sentence from book
            $table->integer('chapter_number')->nullable();
            $table->integer('page_number')->nullable();
            $table->enum('mastery_level', ['new', 'learning', 'practiced', 'mastered'])->default('new');
            $table->integer('lookup_count')->default(1);
            $table->timestamp('first_encountered_at');
            $table->timestamp('last_practiced_at')->nullable();
            $table->timestamp('mastered_at')->nullable();
            $table->text('user_notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'book_id', 'word']);
            $table->index(['user_id', 'mastery_level']);
            $table->index(['book_id', 'chapter_number']);
        });

        Schema::create('reading_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('goal_type', 100); // books_per_month, pages_per_day, complete_book, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('target_value'); // target number (books, pages, etc.)
            $table->integer('current_value')->default(0); // current progress
            $table->date('start_date');
            $table->date('target_date');
            $table->boolean('is_active')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->json('related_books')->nullable(); // book IDs if goal is book-specific
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'goal_type']);
            $table->index('target_date');
        });

        Schema::create('user_reading_info', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->json('reading_preferences')->nullable();
            $table->decimal('estimated_reading_speed', 5, 2)->nullable(); // pages per hour
            $table->integer('daily_reading_goal_minutes')->nullable();
            $table->time('preferred_reading_time')->nullable();
            $table->json('favorite_book_categories')->nullable();
            $table->json('completed_achievements')->nullable();
            $table->integer('total_books_read')->default(0);
            $table->integer('current_reading_streak')->default(0);
            $table->date('last_reading_date')->nullable();
            $table->timestamps();
        });

        Schema::create('book_reading_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->integer('start_page');
            $table->integer('end_page');
            $table->integer('pages_read');
            $table->integer('duration_minutes');
            $table->decimal('reading_speed', 5, 2)->nullable(); // calculated pages per hour
            $table->integer('comprehension_self_rating')->nullable(); // 1-5 self-rating
            $table->text('session_notes')->nullable();
            $table->json('vocabulary_encountered')->nullable(); // new words encountered
            $table->json('questions_raised')->nullable(); // questions that came up during reading
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'book_id']);
            $table->index(['user_id', 'started_at']);
            $table->index('started_at');
        });

        Schema::create('quiz_question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->integer('chapter_number')->nullable(); // Reference to table_of_contents
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay', 'short_answer']);
            $table->json('options')->nullable(); // for multiple choice
            $table->text('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('learning_objective')->nullable();
            $table->enum('cognitive_level', [
                'remember', 'understand', 'apply', 'analyze', 'evaluate', 'create',
            ])->default('understand');
            $table->integer('page_reference')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['book_id', 'question_type']);
            $table->index(['book_id', 'difficulty']);
            $table->index(['book_id', 'chapter_number']);
        });

        // Add indexes to existing book_reading_progress table for better performance
        Schema::table('book_reading_progress', function (Blueprint $table) {
            $table->index(['user_id', 'last_read_at']);
            $table->index(['book_id', 'last_read_at']);
        });

        // Add composite indexes for common quiz queries
        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->index(['book_id', 'status', 'completed_at']);
            $table->index(['user_id', 'book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
        Schema::dropIfExists('reading_achievements');
        Schema::table('book_reading_progress', function (Blueprint $table) {
            $table->dropColumn(['comprehension_score', 'quiz_history', 'reading_streak_days', 'learning_notes']);
        });
        Schema::dropIfExists('book_discussions');
        Schema::dropIfExists('book_discussion_replies');
        Schema::dropIfExists('vocabulary_progress');
        Schema::dropIfExists('reading_goals');

        Schema::dropIfExists('user_reading_info');

        Schema::dropIfExists('book_reading_sessions');

        Schema::dropIfExists('quiz_question_bank');

        Schema::table('book_reading_progress', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'last_read_at']);
            $table->dropIndex(['book_id', 'last_read_at']);
        });

        Schema::table('quiz_sessions', function (Blueprint $table) {
            $table->dropIndex(['book_id', 'status', 'completed_at']);
            $table->dropIndex(['user_id', 'book_id', 'status']);
        });
    }
};
