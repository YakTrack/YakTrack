<?php

namespace Database\Seeds;

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\Task;
use App\Models\TestRun;
use App\Models\TestResult;
use App\Models\User;
use App\TestResultStatus;
use Illuminate\Database\Seeder;

class AcceptanceCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $project = Project::first();

        if (!$user || !$project) {
            $this->command->info('No user or project found. Please run UserSeeder and ProjectSeeder first.');
            return;
        }

        // Create sample acceptance criteria
        $criteria1 = AcceptanceCriteria::create([
            'project_id' => $project->id,
            'code' => 'AC-001',
            'name' => 'User Authentication',
            'description' => 'Users should be able to log in with valid credentials and log out securely.',
            'is_active' => true,
        ]);

        $criteria2 = AcceptanceCriteria::create([
            'project_id' => $project->id,
            'code' => 'AC-002',
            'name' => 'Dashboard Display',
            'description' => 'The dashboard should display relevant project information and navigation options.',
            'is_active' => true,
        ]);

        $criteria3 = AcceptanceCriteria::create([
            'project_id' => $project->id,
            'code' => 'AC-003',
            'name' => 'Task Management',
            'description' => 'Users should be able to create, edit, and delete tasks within projects.',
            'is_active' => true,
        ]);

        // Create initial versions for each criteria
        $criteria1->createVersion([
            'code' => 'AC-001',
            'name' => 'User Authentication',
            'description' => 'Users should be able to log in with valid credentials and log out securely.',
        ], $user->id);

        $criteria2->createVersion([
            'code' => 'AC-002',
            'name' => 'Dashboard Display',
            'description' => 'The dashboard should display relevant project information and navigation options.',
        ], $user->id);

        $criteria3->createVersion([
            'code' => 'AC-003',
            'name' => 'Task Management',
            'description' => 'Users should be able to create, edit, and delete tasks within projects.',
        ], $user->id);

        // Link criteria to existing tasks if any
        $tasks = Task::where('project_id', $project->id)->limit(2)->get();
        if ($tasks->count() > 0) {
            $criteria1->tasks()->attach($tasks->first()->id);
            if ($tasks->count() > 1) {
                $criteria2->tasks()->attach($tasks->skip(1)->first()->id);
            }
        }

        // Create a sample test run
        $testRun = TestRun::create([
            'project_id' => $project->id,
            'name' => 'Sprint 1 Testing',
            'description' => 'Initial testing of core functionality',
            'executed_at' => now()->subDays(2),
            'executed_by_user_id' => $user->id,
        ]);

        // Create test results
        TestResult::create([
            'test_run_id' => $testRun->id,
            'acceptance_criteria_id' => $criteria1->id,
            'acceptance_criteria_version_id' => $criteria1->getCurrentVersion()->id,
            'status' => TestResultStatus::Passed,
            'notes' => 'Login and logout functionality working correctly.',
        ]);

        TestResult::create([
            'test_run_id' => $testRun->id,
            'acceptance_criteria_id' => $criteria2->id,
            'acceptance_criteria_version_id' => $criteria2->getCurrentVersion()->id,
            'status' => TestResultStatus::Failed,
            'notes' => 'Dashboard layout needs adjustment for mobile devices.',
        ]);

        TestResult::create([
            'test_run_id' => $testRun->id,
            'acceptance_criteria_id' => $criteria3->id,
            'acceptance_criteria_version_id' => $criteria3->getCurrentVersion()->id,
            'status' => TestResultStatus::Skipped,
            'notes' => 'Task management features not yet implemented.',
        ]);

        $this->command->info('Acceptance criteria system seeded successfully!');
        $this->command->info('Created 3 acceptance criteria, 1 test run, and 3 test results.');
    }
}