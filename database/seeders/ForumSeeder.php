<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Forum\ForumCategory;
use App\Models\Forum\ForumTopic;
use App\Models\Forum\ForumPost;
use App\Models\User;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use Illuminate\Support\Str;

class ForumSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        if (!$user) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        // Create general categories (only if they don't exist)
        $generalCategories = [
            [
                'name' => 'General Discussion',
                'description' => 'General academic discussions and questions',
                'color' => 'green',
                'sort_order' => 1
            ],
            [
                'name' => 'Study Groups',
                'description' => 'Find and organize study groups',
                'color' => 'purple',
                'sort_order' => 2
            ],
            [
                'name' => 'Book Reviews',
                'description' => 'Share your thoughts on books and resources',
                'color' => 'yellow',
                'sort_order' => 3
            ],
            [
                'name' => 'Help & Support',
                'description' => 'Get help with platform features and technical issues',
                'color' => 'red',
                'sort_order' => 4
            ],
            [
                'name' => 'Announcements',
                'description' => 'Important announcements and updates',
                'color' => 'indigo',
                'is_private' => true,
                'sort_order' => 5
            ]
        ];

        foreach ($generalCategories as $categoryData) {
            $category = ForumCategory::firstOrCreate([
                'name' => $categoryData['name'],
            ], array_merge($categoryData, [
                'slug' => $this->generateUniqueSlug($categoryData['name']),
                'is_active' => true
            ]));

            // Only create sample topics if category was just created
            if ($category->wasRecentlyCreated) {
                $this->createSampleTopics($category, $user);
            }
        }

        // Create academic level categories if they exist (only if they don't exist)
        $academicLevels = AcademicLevel::limit(3)->get(); // Limit to prevent too many categories
        foreach ($academicLevels as $level) {
            $category = ForumCategory::firstOrCreate([
                'academic_level_id' => $level->id,
                'academic_subject_id' => null,
            ], [
                'name' => $level->name . ' Discussion',
                'slug' => $this->generateUniqueSlug($level->name . ' Discussion'),
                'description' => 'General discussions for ' . $level->name . ' level students',
                'color' => 'violet',
                'is_active' => true,
                'sort_order' => 100 + $level->id
            ]);

            if ($category->wasRecentlyCreated) {
                $this->createSampleTopics($category, $user);
            }

            // Create subject-specific categories (limit to 2 per level)
            $subjects = AcademicSubject::where('academic_level_id', $level->id)->limit(2)->get();
            foreach ($subjects as $subject) {
                $subjectCategory = ForumCategory::firstOrCreate([
                    'academic_level_id' => $level->id,
                    'academic_subject_id' => $subject->id,
                ], [
                    'name' => $subject->name . ' - ' . $level->name,
                    'slug' => $this->generateUniqueSlug($subject->name . ' ' . $level->name),
                    'description' => 'Subject-specific discussions for ' . $subject->name . ' at ' . $level->name . ' level',
                    'color' => 'blue',
                    'is_active' => true,
                    'sort_order' => ($level->id * 100) + $subject->id
                ]);

                if ($subjectCategory->wasRecentlyCreated) {
                    $this->createSampleTopics($subjectCategory, $user);
                }
            }
        }

        $this->command->info('Forum categories and sample topics created successfully!');
    }

    private function generateUniqueSlug($name, $attempt = 0)
    {
        $baseSlug = Str::slug($name);
        $slug = $attempt > 0 ? $baseSlug . '-' . $attempt : $baseSlug;

        if (ForumCategory::where('slug', $slug)->exists()) {
            return $this->generateUniqueSlug($name, $attempt + 1);
        }

        return $slug;
    }

    private function createSampleTopics($category, $user)
    {
        $sampleTopics = [
            [
                'title' => 'Welcome to ' . $category->name,
                'content' => 'Welcome to the ' . $category->name . ' category! This is a great place to discuss topics related to ' . $category->description . '. Feel free to ask questions, share insights, and help fellow students.',
                'is_pinned' => true
            ],
            [
                'title' => 'Study Tips and Techniques',
                'content' => 'Share your best study tips and techniques that have worked for you. What methods do you use to stay organized and productive?',
                'tags' => ['study-tips', 'productivity', 'learning']
            ]
        ];

        foreach ($sampleTopics as $topicData) {
            $topic = ForumTopic::create([
                'title' => $topicData['title'],
                'slug' => $this->generateUniqueTopicSlug($topicData['title']),
                'forum_category_id' => $category->id,
                'user_id' => $user->id,
                'is_pinned' => $topicData['is_pinned'] ?? false,
                'tags' => $topicData['tags'] ?? [],
                'academic_level_id' => $category->academic_level_id,
                'academic_subject_id' => $category->academic_subject_id,
                'last_activity_at' => now(),
                'views_count' => rand(10, 50)
            ]);

            // Create first post
            ForumPost::create([
                'content' => $topicData['content'],
                'forum_topic_id' => $topic->id,
                'user_id' => $user->id,
                'is_approved' => true,
                'likes_count' => rand(0, 5)
            ]);
        }
    }

    private function generateUniqueTopicSlug($title, $attempt = 0)
    {
        $baseSlug = Str::slug($title);
        $slug = $attempt > 0 ? $baseSlug . '-' . $attempt : $baseSlug;

        if (ForumTopic::where('slug', $slug)->exists()) {
            return $this->generateUniqueTopicSlug($title, $attempt + 1);
        }

        return $slug;
    }
}
