<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Departments typical for a remote-first software house / digital agency.
     */
    private static array $departments = [
        'Engineering' => [
            'description' => 'Responsible for software development, architecture, code review, and technical delivery across all products and client projects.',
        ],
        'Product' => [
            'description' => 'Owns product strategy, roadmap prioritization, and cross-functional collaboration to ensure value delivery to users and clients.',
        ],
        'Design' => [
            'description' => 'Handles UI/UX design, brand identity, design systems, and user research to create intuitive digital experiences.',
        ],
        'Quality Assurance (QA)' => [
            'description' => 'Ensures software quality through manual and automated testing, bug tracking, and release validation.',
        ],
        'DevOps & Infrastructure' => [
            'description' => 'Manages cloud infrastructure, CI/CD pipelines, monitoring, and system reliability across all environments.',
        ],
        'Human Resources (HR)' => [
            'description' => 'Oversees talent acquisition, people operations, culture, remote onboarding, and employee well-being.',
        ],
        'Finance & Accounting' => [
            'description' => 'Handles payroll processing, financial reporting, budgeting, invoicing, and compliance.',
        ],
        'Sales & Business Development' => [
            'description' => 'Drives client acquisition, partnership development, and revenue growth through strategic outreach.',
        ],
        'Marketing & Growth' => [
            'description' => 'Leads digital marketing campaigns, content strategy, SEO, and brand awareness across channels.',
        ],
        'Project Management (PMO)' => [
            'description' => 'Coordinates project delivery, manages timelines, stakeholder communication, and process improvement.',
        ],
    ];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(array_keys(self::$departments));

        return [
            'name'        => $name,
            'description' => self::$departments[$name]['description'],
        ];
    }
}
