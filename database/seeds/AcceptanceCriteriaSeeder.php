<?php

namespace Database\Seeds;

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\Task;
use App\Models\TestResult;
use App\Models\TestRun;
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
            return;
        }

        // Create sample acceptance criteria
        $criteria1 = AcceptanceCriteria::create([
            'project_id'  => $project->id,
            'code'        => 'AC-001',
            'name'        => 'User Authentication',
            'description' => 'Users should be able to log in with valid credentials and log out securely.',
            'is_active'   => true,
        ]);

        $criteria2 = AcceptanceCriteria::create([
            'project_id'  => $project->id,
            'code'        => 'AC-002',
            'name'        => 'Dashboard Display',
            'description' => 'The dashboard should display relevant project information and navigation options.',
            'is_active'   => true,
        ]);

        $criteria3 = AcceptanceCriteria::create([
            'project_id'  => $project->id,
            'code'        => 'AC-003',
            'name'        => 'Task Management',
            'description' => 'Users should be able to create, edit, and delete tasks within projects.',
            'is_active'   => true,
        ]);

        $criteria4 = AcceptanceCriteria::create([
            'project_id'  => $project->id,
            'code'        => 'AC-004',
            'name'        => 'Data Export',
            'description' => 'Users should be able to export project data in CSV and PDF formats.',
            'is_active'   => true,
        ]);

        $criteria5 = AcceptanceCriteria::create([
            'project_id'  => $project->id,
            'code'        => 'AC-005',
            'name'        => 'Search Functionality',
            'description' => 'Users should be able to search across projects, tasks, and users with filters.',
            'is_active'   => true,
        ]);

        $criteria6 = AcceptanceCriteria::create([
            'project_id'  => $project->id,
            'code'        => 'AC-006',
            'name'        => 'Notification System',
            'description' => 'Users should receive notifications for important events and updates.',
            'is_active'   => true,
        ]);

        // Create initial versions for each criteria
        $criteria1->createVersion([
            'code'        => 'AC-001',
            'name'        => 'User Authentication',
            'description' => 'Users should be able to log in with valid credentials and log out securely.',
        ], $user->id);

        $criteria2->createVersion([
            'code'        => 'AC-002',
            'name'        => 'Dashboard Display',
            'description' => 'The dashboard should display relevant project information and navigation options.',
        ], $user->id);

        $criteria3->createVersion([
            'code'        => 'AC-003',
            'name'        => 'Task Management',
            'description' => 'Users should be able to create, edit, and delete tasks within projects.',
        ], $user->id);

        $criteria4->createVersion([
            'code'        => 'AC-004',
            'name'        => 'Data Export',
            'description' => 'Users should be able to export project data in CSV and PDF formats.',
        ], $user->id);

        $criteria5->createVersion([
            'code'        => 'AC-005',
            'name'        => 'Search Functionality',
            'description' => 'Users should be able to search across projects, tasks, and users with filters.',
        ], $user->id);

        $criteria6->createVersion([
            'code'        => 'AC-006',
            'name'        => 'Notification System',
            'description' => 'Users should receive notifications for important events and updates.',
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
            'project_id'          => $project->id,
            'name'                => 'Sprint 1 Testing',
            'description'         => 'Initial testing of core functionality',
            'executed_at'         => now()->subDays(2),
            'executed_by_user_id' => $user->id,
        ]);

        // Create test results for first test run
        TestResult::create([
            'test_run_id'                    => $testRun->id,
            'acceptance_criteria_id'         => $criteria1->id,
            'acceptance_criteria_version_id' => $criteria1->getCurrentVersion()->id,
            'status'                         => TestResultStatus::Passed,
            'notes'                          => 'Login and logout functionality working correctly.',
        ]);

        TestResult::create([
            'test_run_id'                    => $testRun->id,
            'acceptance_criteria_id'         => $criteria2->id,
            'acceptance_criteria_version_id' => $criteria2->getCurrentVersion()->id,
            'status'                         => TestResultStatus::Failed,
            'notes'                          => 'Dashboard layout needs adjustment for mobile devices.',
        ]);

        TestResult::create([
            'test_run_id'                    => $testRun->id,
            'acceptance_criteria_id'         => $criteria3->id,
            'acceptance_criteria_version_id' => $criteria3->getCurrentVersion()->id,
            'status'                         => TestResultStatus::Pending,
            'notes'                          => 'Task management features in progress.',
        ]);

        // Create a second test run with pending statuses (demonstrating the new default)
        $testRun2 = TestRun::create([
            'project_id'          => $project->id,
            'name'                => 'Sprint 2 Testing',
            'description'         => 'Testing new features - many tests pending',
            'executed_at'         => now()->subDays(1),
            'executed_by_user_id' => $user->id,
        ]);

        // Create test results with various statuses, including pending (the new default)
        TestResult::create([
            'test_run_id'                    => $testRun2->id,
            'acceptance_criteria_id'         => $criteria4->id,
            'acceptance_criteria_version_id' => $criteria4->getCurrentVersion()->id,
            'status'                         => TestResultStatus::Pending,
            'notes'                          => null,
        ]);

        TestResult::create([
            'test_run_id'                    => $testRun2->id,
            'acceptance_criteria_id'         => $criteria5->id,
            'acceptance_criteria_version_id' => $criteria5->getCurrentVersion()->id,
            'status'                         => TestResultStatus::Pending,
            'notes'                          => null,
        ]);

        TestResult::create([
            'test_run_id'                    => $testRun2->id,
            'acceptance_criteria_id'         => $criteria6->id,
            'acceptance_criteria_version_id' => $criteria6->getCurrentVersion()->id,
            'status'                         => TestResultStatus::Blocked,
            'notes'                          => 'Waiting for API integration to be completed.',
        ]);
    }
}
