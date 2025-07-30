<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        // Create 30 authors with different profiles

        // 1. Create 10 established authors with awards
        $this->createEstablishedAuthors();

        // 2. Create 10 emerging authors
        $this->createEmergingAuthors();

        // 3. Create 5 academic authors
        $this->createAcademicAuthors();

        // 4. Create 5 celebrity/popular authors
        $this->createPopularAuthors();
    }

    private function createEstablishedAuthors(): void
    {
        $establishedAuthors = [
            [
                'name' => 'Margaret Atwood',
                'pen_name' => null,
                'email' => 'margaret.atwood@example.com',
                'biography' => 'Margaret Atwood is a Canadian poet, novelist, literary critic, essayist, teacher, environmental activist, and inventor. She has published seventeen novels, eight collections of short fiction, eight books of poetry, eleven works of non-fiction, and nine collections of childrens literature.',
                'education' => 'Master of Arts in English Literature from Harvard University',
                'awards' => 'Booker Prize Winner (2000), Governor General\'s Award (1985, 1996)',
                'writing_experience' => 45,
                'social_links' => json_encode([
                    'twitter' => 'https://twitter.com/MargaretAtwood',
                    'website' => 'https://margaretatwood.ca',
                    'goodreads' => 'https://goodreads.com/author/show/3472.Margaret_Atwood'
                ]),
                'author_statement' => 'Writing is a process of discovery. You don\'t know what you\'re going to write until you write it.'
            ],
            [
                'name' => 'Haruki Murakami',
                'pen_name' => null,
                'email' => 'haruki.murakami@example.com',
                'biography' => 'Haruki Murakami is a Japanese writer. His novels, essays, and short stories have been bestsellers in Japan as well as internationally, with his work being translated into 50 languages and selling millions of copies outside his native country.',
                'education' => 'Bachelor of Arts in Drama from Waseda University',
                'awards' => 'Franz Kafka Prize (2006), World Fantasy Award (2006)',
                'writing_experience' => 35,
                'social_links' => json_encode([
                    'website' => 'https://harukimurakami.com',
                    'goodreads' => 'https://goodreads.com/author/show/3354.Haruki_Murakami'
                ]),
                'author_statement' => 'If you only read the books that everyone else is reading, you can only think what everyone else is thinking.'
            ],
            [
                'name' => 'Toni Morrison',
                'pen_name' => null,
                'email' => 'toni.morrison@example.com',
                'biography' => 'Toni Morrison was an American novelist, essayist, book editor, and college professor. Her first novel, The Bluest Eye, was published in 1970. The critically acclaimed Song of Solomon brought her national attention and won the National Book Critics Circle Award.',
                'education' => 'Master of Arts in English from Cornell University',
                'awards' => 'Nobel Prize in Literature (1993), Pulitzer Prize for Fiction (1988)',
                'writing_experience' => 40,
                'social_links' => json_encode([
                    'website' => 'https://tonimorrison.com',
                    'goodreads' => 'https://goodreads.com/author/show/14470.Toni_Morrison'
                ]),
                'author_statement' => 'If there\'s a book that you want to read, but it hasn\'t been written yet, then you must write it.'
            ]
        ];

        foreach ($establishedAuthors as $authorData) {
            $user = User::firstOrCreate(
                [
                    'name' => $authorData['name'],
                    'email' => $authorData['email'],
                ],
                [
                'name' => $authorData['name'],
                'email' => $authorData['email'],
                'password' => Hash::make('password'),
                'role' => 'author',
                'email_verified_at' => now(),
            ]);

            Author::firstOrCreate([
                'user_id' => $user->id,
                'name' => $authorData['name'],
                'pen_name' => $authorData['pen_name'],
                'biography' => $authorData['biography'],
                'education' => $authorData['education'],
                'awards' => $authorData['awards'],
                'writing_experience' => $authorData['writing_experience'],
                'social_links' => $authorData['social_links'],
                'author_statement' => $authorData['author_statement'],
                'website' => $authorData['social_links'] ? json_decode($authorData['social_links'], true)['website'] ?? null : null,
            ]);
        }

        // Create 7 more established authors using factory
        User::factory(7)->create([
            'role' => 'author'
        ])->each(function ($user) {
            Author::factory()->awardWinner()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    private function createEmergingAuthors(): void
    {
        $emergingAuthors = [
            [
                'name' => 'Sofia Chen',
                'pen_name' => 'S.L. Chen',
                'email' => 'sofia.chen@example.com',
                'biography' => 'Sofia Chen is an emerging voice in contemporary fiction. Her debut novel exploring themes of identity and belonging in modern multicultural society has garnered critical acclaim and a growing readership.',
                'education' => 'Master of Fine Arts in Creative Writing from Columbia University',
                'awards' => null,
                'writing_experience' => 3,
                'social_links' => json_encode([
                    'twitter' => 'https://twitter.com/SofiaChenWrites',
                    'instagram' => 'https://instagram.com/sofiachenauthor',
                    'website' => 'https://sofiachenwriter.com'
                ]),
                'author_statement' => 'I write to explore the spaces between cultures, the places where identity becomes fluid and stories find their truth.'
            ],
            [
                'name' => 'Marcus Johnson',
                'pen_name' => null,
                'email' => 'marcus.johnson@example.com',
                'biography' => 'Marcus Johnson is a debut novelist whose raw, authentic voice captures the urban experience with unprecedented honesty. His work focuses on social justice themes and community resilience.',
                'education' => 'Bachelor of Arts in Journalism from Howard University',
                'awards' => null,
                'writing_experience' => 2,
                'social_links' => json_encode([
                    'twitter' => 'https://twitter.com/MarcusJWrites',
                    'facebook' => 'https://facebook.com/marcusjohnsonwriter'
                ]),
                'author_statement' => 'Every story I tell is a testament to the strength and beauty of communities that mainstream media often overlooks.'
            ]
        ];

        foreach ($emergingAuthors as $authorData) {
            $user = User::firstOrCreate(
                [
                    'name' => $authorData['name'],
                    'email' => $authorData['email'],
                ],
                [
                'name' => $authorData['name'],
                'email' => $authorData['email'],
                'password' => Hash::make('password'),
                'role' => 'author',
                'email_verified_at' => now(),
            ]);

            Author::firstOrCreate([
                'user_id' => $user->id,
                'name' => $authorData['name'],
                'pen_name' => $authorData['pen_name'],
                'biography' => $authorData['biography'],
                'education' => $authorData['education'],
                'awards' => $authorData['awards'],
                'writing_experience' => $authorData['writing_experience'],
                'social_links' => $authorData['social_links'],
                'author_statement' => $authorData['author_statement'],
                'website' => $authorData['social_links'] ? json_decode($authorData['social_links'], true)['website'] ?? null : null,
            ]);
        }

        // Create 8 more emerging authors using factory
        User::factory(8)->create([
            'role' => 'author'
        ])->each(function ($user) {
            Author::factory()->emerging()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    private function createAcademicAuthors(): void
    {
        $academicAuthors = [
            [
                'name' => 'Dr. Elizabeth Harper',
                'pen_name' => null,
                'email' => 'elizabeth.harper@example.com',
                'biography' => 'Dr. Elizabeth Harper is a Professor of Literature at Yale University and a renowned scholar in Victorian literature. She has published extensively on 19th-century women writers and their contribution to the literary canon.',
                'education' => 'Ph.D. in English Literature from Oxford University',
                'awards' => 'Modern Language Association Award for Scholarly Achievement (2018)',
                'writing_experience' => 15,
                'social_links' => json_encode([
                    'linkedin' => 'https://linkedin.com/in/elizabethharperliterature',
                    'website' => 'https://elizabethharper.yale.edu'
                ]),
                'author_statement' => 'Academic writing should bridge the gap between scholarly research and public understanding, making literature accessible to all.'
            ]
        ];

        foreach ($academicAuthors as $authorData) {
            $user = User::firstOrCreate(
                [
                    'name' => $authorData['name'],
                    'email' => $authorData['email'],
                ],
                [
                'name' => $authorData['name'],
                'email' => $authorData['email'],
                'password' => Hash::make('password'),
                'role' => 'author',
                'email_verified_at' => now(),
            ]);

            Author::firstOrCreate([
                'user_id' => $user->id,
                'name' => $authorData['name'],
                'pen_name' => $authorData['pen_name'],
                'biography' => $authorData['biography'],
                'education' => $authorData['education'],
                'awards' => $authorData['awards'],
                'writing_experience' => $authorData['writing_experience'],
                'social_links' => $authorData['social_links'],
                'author_statement' => $authorData['author_statement'],
                'website' => $authorData['social_links'] ? json_decode($authorData['social_links'], true)['website'] ?? null : null,
            ]);
        }

        // Create 4 more academic authors using factory
        User::factory(4)->create([
            'role' => 'author'
        ])->each(function ($user) {
            Author::factory()->academic()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    private function createPopularAuthors(): void
    {
        $popularAuthors = [
            [
                'name' => 'Alexandra Reed',
                'pen_name' => 'Alex Reed',
                'email' => 'alexandra.reed@example.com',
                'biography' => 'Alexandra Reed is a bestselling author of contemporary romance novels. With over 20 books published, she has captured the hearts of millions of readers worldwide with her compelling characters and emotional storytelling.',
                'education' => 'Bachelor of Arts in Creative Writing from University of California, Los Angeles',
                'awards' => 'Romance Writers of America RITA Award Winner (2019, 2021)',
                'writing_experience' => 12,
                'social_links' => json_encode([
                    'twitter' => 'https://twitter.com/AlexReedAuthor',
                    'facebook' => 'https://facebook.com/alexandrareedbooks',
                    'instagram' => 'https://instagram.com/alexreedwrites',
                    'website' => 'https://alexandrareedbooks.com',
                    'goodreads' => 'https://goodreads.com/author/show/alexreed'
                ]),
                'author_statement' => 'Love stories have the power to heal, inspire, and remind us of our shared humanity. I write to celebrate the magic of human connection.'
            ],
            [
                'name' => 'James Mitchell',
                'pen_name' => 'J.M. Steel',
                'email' => 'james.mitchell@example.com',
                'biography' => 'Writing under the pen name J.M. Steel, James Mitchell is a prolific thriller and mystery writer. His fast-paced novels and intricate plot twists have earned him a dedicated following and multiple film adaptations.',
                'education' => 'Bachelor of Arts in Criminal Justice from John Jay College',
                'awards' => 'Edgar Award for Best Thriller (2020)',
                'writing_experience' => 18,
                'social_links' => json_encode([
                    'twitter' => 'https://twitter.com/JMSteelBooks',
                    'website' => 'https://jmsteelthrillers.com',
                    'youtube' => 'https://youtube.com/c/JMSteelAuthor'
                ]),
                'author_statement' => 'A great thriller should keep you guessing until the very last page, then leave you immediately wanting more.'
            ]
        ];

        foreach ($popularAuthors as $authorData) {
            $user = User::firstOrCreate(
                [
                    'name' => $authorData['name'],
                    'email' => $authorData['email'],
                ],
                [
                'name' => $authorData['name'],
                'email' => $authorData['email'],
                'password' => Hash::make('password'),
                'role' => 'author',
                'email_verified_at' => now(),
            ]);

            Author::firstOrCreate([
                'user_id' => $user->id,
                'name' => $authorData['name'],
                'pen_name' => $authorData['pen_name'],
                'biography' => $authorData['biography'],
                'education' => $authorData['education'],
                'awards' => $authorData['awards'],
                'writing_experience' => $authorData['writing_experience'],
                'social_links' => $authorData['social_links'],
                'author_statement' => $authorData['author_statement'],
                'website' => $authorData['social_links'] ? json_decode($authorData['social_links'], true)['website'] ?? null : null,
            ]);
        }

        // Create 3 more popular authors using factory
        User::factory(3)->create([
            'role' => 'author'
        ])->each(function ($user) {
            Author::factory()->create([
                'user_id' => $user->id,
                'writing_experience' => fake()->numberBetween(8, 20),
                'awards' => fake()->boolean(70) ? 'New York Times Bestselling Author' : null,
            ]);
        });
    }
}
