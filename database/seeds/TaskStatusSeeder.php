<?php

use App\Models\Project;
use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusConfigs = [
            ['name' => 'To Do', 'color' => '#94a3b8', 'sort_order' => 1, 'is_default' => true, 'is_completed' => false, 'is_closed' => false],
            ['name' => 'In Progress', 'color' => '#3b82f6', 'sort_order' => 2, 'is_default' => false, 'is_completed' => false, 'is_closed' => false],
            ['name' => 'In Review', 'color' => '#f59e0b', 'sort_order' => 3, 'is_default' => false, 'is_completed' => false, 'is_closed' => false],
            ['name' => 'Blocked', 'color' => '#ef4444', 'sort_order' => 4, 'is_default' => false, 'is_completed' => false, 'is_closed' => false],
            ['name' => 'Done', 'color' => '#10b981', 'sort_order' => 5, 'is_default' => false, 'is_completed' => true, 'is_closed' => false],
            ['name' => 'Closed', 'color' => '#8b5cf6', 'sort_order' => 6, 'is_default' => false, 'is_completed' => false, 'is_closed' => true],
        ];

        Project::all()->each(function ($project) use ($statusConfigs) {
            foreach ($statusConfigs as $config) {
                TaskStatus::create([
                    'project_id'   => $project->id,
                    'name'         => $config['name'],
                    'color'        => $config['color'],
                    'sort_order'   => $config['sort_order'],
                    'is_default'   => $config['is_default'],
                    'is_completed' => $config['is_completed'],
                    'is_closed'    => $config['is_closed'],
                ]);
            }
        });
    }
}
