<?php

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create tasks for sprints
        Sprint::with('projects')->get()->each(function ($sprint) {
            $project = $sprint->projects->first();
            if ($project) {
                Task::factory(3)->create(['project_id' => $project->id]);
            }
        });

        // Create additional demo tasks for the first project to showcase kanban board
        $firstProject = Project::first();

        if ($firstProject) {
            $statuses = $firstProject->taskStatuses;

            if ($statuses->isNotEmpty()) {
                // Get statuses by name
                $todoStatus = $statuses->firstWhere('name', 'To Do');
                $inProgressStatus = $statuses->firstWhere('name', 'In Progress');
                $inReviewStatus = $statuses->firstWhere('name', 'In Review');
                $blockedStatus = $statuses->firstWhere('name', 'Blocked');
                $doneStatus = $statuses->firstWhere('name', 'Done');

                // Demo tasks across statuses

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Setup development environment',
                    'description' => 'Configure local development environment with all necessary tools',
                    'status_id'   => $todoStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Design homepage mockup',
                    'description' => 'Create initial design mockups for homepage',
                    'status_id'   => $todoStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Implement user authentication',
                    'description' => 'Build complete authentication system',
                    'status_id'   => $inProgressStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Create login form',
                    'description' => 'Build login UI component',
                    'status_id'   => $doneStatus?->id,
                    'status'      => 'complete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Add password reset functionality',
                    'description' => 'Implement forgot password flow',
                    'status_id'   => $inProgressStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Setup email verification',
                    'description' => 'Configure email verification for new users',
                    'status_id'   => $todoStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Build REST API endpoints',
                    'description' => 'Create RESTful API for mobile app',
                    'status_id'   => $inProgressStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Create user endpoints',
                    'description' => 'CRUD operations for users',
                    'status_id'   => $inReviewStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Add API documentation',
                    'description' => 'Document all API endpoints',
                    'status_id'   => $todoStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Write unit tests for core features',
                    'description' => 'Achieve 80% code coverage',
                    'status_id'   => $inReviewStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Update documentation',
                    'description' => 'Update README and developer docs',
                    'status_id'   => $inReviewStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Integrate payment gateway',
                    'description' => 'Waiting for API credentials from payment provider',
                    'status_id'   => $blockedStatus?->id,
                    'status'      => 'incomplete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Setup project repository',
                    'description' => 'Initialize git repository and CI/CD pipeline',
                    'status_id'   => $doneStatus?->id,
                    'status'      => 'complete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Configure database schema',
                    'description' => 'Design and implement initial database structure',
                    'status_id'   => $doneStatus?->id,
                    'status'      => 'complete',
                ]);

                Task::create([
                    'project_id'  => $firstProject->id,
                    'name'        => 'Setup CI/CD pipeline',
                    'description' => 'Configure automated testing and deployment',
                    'status_id'   => $doneStatus?->id,
                    'status'      => 'complete',
                ]);
            }
        }
    }
}
