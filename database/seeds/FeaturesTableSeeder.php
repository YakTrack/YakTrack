<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Project;
use Illuminate\Database\Seeder;

class FeaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::all()->each(function ($project) {
            // Create a few features per project with codes
            Feature::factory()->withCode('FEAT-001')->forProject($project)->create([
                'name'        => 'User Authentication',
                'description' => 'Features related to user login, logout, and authentication.',
            ]);

            Feature::factory()->withCode('FEAT-002')->forProject($project)->create([
                'name'        => 'Dashboard',
                'description' => 'Main dashboard features including overview, navigation, and quick actions.',
            ]);

            Feature::factory()->withCode('FEAT-003')->forProject($project)->create([
                'name'        => 'Task Management',
                'description' => 'Features for creating, editing, and managing tasks within projects.',
            ]);

            // Create a feature without a code to show variety
            Feature::factory()->forProject($project)->create([
                'name'        => 'Reporting',
                'description' => 'Features for generating reports and analytics.',
                'code'        => null,
            ]);
        });
    }
}
