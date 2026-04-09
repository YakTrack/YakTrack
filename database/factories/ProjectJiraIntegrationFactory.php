<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectJiraIntegration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectJiraIntegration>
 */
class ProjectJiraIntegrationFactory extends Factory
{
    protected $model = ProjectJiraIntegration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id'    => Project::factory(),
            'site_host'     => 'acme.atlassian.net',
            'account_email' => 'dev@example.com',
            'api_token'     => 'fake-api-token-12345678',
        ];
    }
}
