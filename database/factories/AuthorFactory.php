<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        $socialPlatforms = ['twitter', 'facebook', 'linkedin', 'instagram', 'website', 'goodreads', 'youtube'];
        $selectedPlatforms = $this->faker->randomElements($socialPlatforms, $this->faker->numberBetween(0, 4));

        $socialLinks = [];
        foreach ($selectedPlatforms as $platform) {
            switch ($platform) {
                case 'twitter':
                    $socialLinks['twitter'] = 'https://twitter.com/' . $this->faker->userName;
                    break;
                case 'facebook':
                    $socialLinks['facebook'] = 'https://facebook.com/' . $this->faker->userName;
                    break;
                case 'linkedin':
                    $socialLinks['linkedin'] = 'https://linkedin.com/in/' . $this->faker->userName;
                    break;
                case 'instagram':
                    $socialLinks['instagram'] = 'https://instagram.com/' . $this->faker->userName;
                    break;
                case 'website':
                    $socialLinks['website'] = 'https://' . $this->faker->domainName;
                    break;
                case 'goodreads':
                    $socialLinks['goodreads'] = 'https://goodreads.com/author/show/' . $this->faker->randomNumber(8);
                    break;
                case 'youtube':
                    $socialLinks['youtube'] = 'https://youtube.com/c/' . $this->faker->userName;
                    break;
            }
        }

        $educationLevels = [
            'Ph.D. in English Literature from Harvard University',
            'Master of Fine Arts in Creative Writing from Stanford University',
            'Bachelor of Arts in Journalism from Columbia University',
            'Ph.D. in History from Oxford University',
            'Master of Arts in Philosophy from Cambridge University',
            'Bachelor of Science in Computer Science from MIT',
            'Ph.D. in Psychology from Yale University',
            'Master of Education from University of California, Berkeley',
            'Bachelor of Arts in Political Science from Princeton University',
            'Ph.D. in Sociology from University of Chicago',
        ];

        $awards = [
            'Pulitzer Prize for Fiction (2019)',
            'National Book Award Winner (2018)',
            'Hugo Award for Best Novel (2020)',
            'Man Booker Prize Nominee (2017)',
            'PEN/Faulkner Award Winner (2021)',
            'National Book Critics Circle Award (2019)',
            'Edgar Award for Best Mystery Novel (2020)',
            'Newbery Medal Winner (2018)',
            'Caldecott Medal Recipient (2019)',
            'Lambda Literary Award Winner (2020)',
        ];

        $writingExperience = $this->faker->numberBetween(3, 25);

        $authorStatements = [
            "Writing is my way of making sense of the world around us. Through storytelling, I hope to connect with readers and explore the depths of human experience.",
            "I believe in the power of words to transform lives and challenge perspectives. Every book I write is an invitation for readers to see the world through different eyes.",
            "My goal as an author is to create stories that resonate long after the last page is turned. Literature should be both entertaining and enlightening.",
            "Through my writing, I aim to give voice to the voiceless and shine light on stories that need to be told. Every character has something important to say.",
            "I write because I must. It's not just my profession, it's my calling. Each story is a piece of my soul shared with the world.",
            "Fiction allows us to explore truths that reality sometimes obscures. I write to reveal these hidden truths and spark meaningful conversations.",
            "My writing journey began as a personal exploration and has evolved into a mission to inspire and educate readers across all walks of life.",
            "I believe every story has the potential to change someone's life. That's the responsibility and privilege I carry as an author.",
        ];

        $biographies = [
            "A passionate storyteller with a gift for weaving intricate narratives that captivate readers from the first page. With years of experience in both fiction and non-fiction, they have established themselves as a distinctive voice in contemporary literature.",
            "An award-winning author whose works have been translated into multiple languages and adapted for screen. Their writing explores themes of identity, belonging, and the human condition with remarkable depth and sensitivity.",
            "A prolific writer known for their ability to blend literary excellence with commercial appeal. Their books consistently appear on bestseller lists while receiving critical acclaim for their innovative storytelling techniques.",
            "A former academic turned full-time author, they bring scholarly rigor to popular fiction. Their background in research and teaching informs their meticulously crafted narratives and well-developed characters.",
            "A versatile writer whose portfolio spans multiple genres, from literary fiction to young adult novels. They have a unique ability to adapt their voice to different audiences while maintaining their distinctive style.",
            "An emerging voice in contemporary literature whose debut novel garnered widespread critical acclaim. Their work focuses on social issues and personal transformation, resonating with readers across generations.",
            "A seasoned author with numerous publications to their credit. Their extensive experience in journalism and creative writing brings authenticity and depth to every story they tell.",
            "A internationally recognized author whose works have been featured in major publications and literary festivals worldwide. They are known for their compelling characters and thought-provoking themes.",
        ];

        return [
            'name' => $this->faker->boolean(30) ? $this->faker->name : null, // 30% chance of having a different professional name
            'pen_name' => $this->faker->boolean(20) ? $this->faker->name : null, // 20% chance of having a pen name
            'biography' => $this->faker->randomElement($biographies),
            'website' => $this->faker->boolean(60) ? 'https://' . $this->faker->domainName : null,
            'social_links' => empty($socialLinks) ? null : json_encode($socialLinks),
            'writing_experience' => $writingExperience,
            'education' => $this->faker->boolean(80) ? $this->faker->randomElement($educationLevels) : null,
            'awards' => $this->faker->boolean(30) ? $this->faker->randomElement($awards) : null,
            'author_statement' => $this->faker->randomElement($authorStatements),
        ];
    }

    /**
     * Create an author with specific characteristics
     */
    public function awardWinner(): static
    {
        return $this->state(function (array $attributes) {
            $awards = [
                'Pulitzer Prize for Fiction (2019)',
                'National Book Award Winner (2018)',
                'Hugo Award for Best Novel (2020)',
                'Man Booker Prize Nominee (2017)',
                'PEN/Faulkner Award Winner (2021)',
            ];

            return [
                'awards' => $this->faker->randomElement($awards),
                'writing_experience' => $this->faker->numberBetween(10, 30),
            ];
        });
    }

    /**
     * Create a new/emerging author
     */
    public function emerging(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'writing_experience' => $this->faker->numberBetween(1, 5),
                'awards' => null,
                'education' => $this->faker->boolean(90) ?
                    'Master of Fine Arts in Creative Writing from ' . $this->faker->randomElement([
                        'Iowa Writers\' Workshop',
                        'Columbia University',
                        'New York University',
                        'University of California, Irvine'
                    ]) : null,
            ];
        });
    }

    /**
     * Create an academic author
     */
    public function academic(): static
    {
        return $this->state(function (array $attributes) {
            $academicEducation = [
                'Ph.D. in English Literature from Harvard University',
                'Ph.D. in History from Oxford University',
                'Ph.D. in Philosophy from Cambridge University',
                'Ph.D. in Psychology from Yale University',
                'Ph.D. in Sociology from University of Chicago',
            ];

            return [
                'education' => $this->faker->randomElement($academicEducation),
                'writing_experience' => $this->faker->numberBetween(8, 25),
                'website' => 'https://www.' . $this->faker->domainName . '.edu',
            ];
        });
    }
}
