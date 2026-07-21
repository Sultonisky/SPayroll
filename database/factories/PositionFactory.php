<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Job titles typical for a remote-first software house / digital agency.
     * Salaries are in IDR. Fulltime is always higher than internship for the same role.
     *
     * Format: 'title' => ['fulltime' => int, 'internship' => int, 'description' => string]
     */
    private static array $positions = [
        // Engineering
        'Junior Software Engineer' => [
            'fulltime'    => 6_000_000,
            'internship'  => 2_000_000,
            'description' => 'Develops and maintains software features under guidance, participates in code reviews, and contributes to technical documentation.',
        ],
        'Software Engineer' => [
            'fulltime'    => 10_000_000,
            'internship'  => 2_500_000,
            'description' => 'Independently designs, develops, and ships product features; collaborates with cross-functional teams in an async-first environment.',
        ],
        'Senior Software Engineer' => [
            'fulltime'    => 16_000_000,
            'internship'  => 3_500_000,
            'description' => 'Leads technical implementation, mentors junior engineers, and drives architectural decisions within the team.',
        ],
        'Tech Lead' => [
            'fulltime'    => 22_000_000,
            'internship'  => 4_000_000,
            'description' => 'Provides technical leadership for a squad, owns codebase quality, and aligns engineering delivery with product goals.',
        ],
        'Engineering Manager' => [
            'fulltime'    => 28_000_000,
            'internship'  => 5_000_000,
            'description' => 'Manages a team of engineers, owns hiring and performance, and partners with Product to define delivery strategy.',
        ],

        // Product
        'Product Designer (UI/UX)' => [
            'fulltime'    => 9_000_000,
            'internship'  => 2_000_000,
            'description' => 'Creates wireframes, prototypes, and high-fidelity designs; conducts user research and maintains the design system.',
        ],
        'Product Manager' => [
            'fulltime'    => 18_000_000,
            'internship'  => 3_000_000,
            'description' => 'Defines product vision, manages backlog, prioritizes features based on data, and aligns stakeholders.',
        ],

        // QA
        'QA Engineer' => [
            'fulltime'    => 8_000_000,
            'internship'  => 1_800_000,
            'description' => 'Designs and executes test plans, reports bugs, and collaborates with engineers to maintain product quality.',
        ],

        // DevOps
        'DevOps Engineer' => [
            'fulltime'    => 14_000_000,
            'internship'  => 3_000_000,
            'description' => 'Manages CI/CD pipelines, cloud infrastructure (AWS/GCP), monitoring, and security practices.',
        ],

        // HR & Finance
        'HR Generalist' => [
            'fulltime'    => 7_000_000,
            'internship'  => 1_500_000,
            'description' => 'Supports recruitment, onboarding, employee relations, and administrative HR processes in a remote setting.',
        ],
        'Finance & Payroll Specialist' => [
            'fulltime'    => 8_000_000,
            'internship'  => 1_800_000,
            'description' => 'Processes payroll, manages invoicing, handles tax compliance, and prepares financial reports.',
        ],

        // Sales & Marketing
        'Business Development Executive' => [
            'fulltime'    => 10_000_000,
            'internship'  => 2_000_000,
            'description' => 'Identifies new business opportunities, nurtures client relationships, and supports proposal and contract processes.',
        ],
        'Digital Marketing Specialist' => [
            'fulltime'    => 8_000_000,
            'internship'  => 1_800_000,
            'description' => 'Runs digital campaigns, manages SEO/SEM, produces content, and tracks marketing performance metrics.',
        ],

        // PMO
        'Project Manager' => [
            'fulltime'    => 14_000_000,
            'internship'  => 2_500_000,
            'description' => 'Plans and delivers projects on time and budget, manages client communication, and facilitates agile ceremonies.',
        ],
        'Scrum Master' => [
            'fulltime'    => 12_000_000,
            'internship'  => 2_000_000,
            'description' => 'Facilitates agile ceremonies, removes team impediments, and coaches squads on Scrum/Kanban practices.',
        ],
    ];

    public function definition(): array
    {
        $title = fake()->unique()->randomElement(array_keys(self::$positions));
        $data  = self::$positions[$title];

        return [
            'name'                   => $title,
            'description'            => $data['description'],
            'base_salary_fulltime'   => $data['fulltime'],
            'base_salary_internship' => $data['internship'],
        ];
    }
}
