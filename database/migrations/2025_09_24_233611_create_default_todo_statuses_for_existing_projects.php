<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default "To Do" status for all existing projects
        $projects = DB::table('projects')->get();

        foreach ($projects as $project) {
            // Check if project already has a "To Do" status
            $existingStatus = DB::table('task_statuses')
                ->where('project_id', $project->id)
                ->where('name', 'To Do')
                ->first();

            if (!$existingStatus) {
                $statusId = DB::table('task_statuses')->insertGetId([
                    'name'         => 'To Do',
                    'color'        => '#6B7280', // Gray color
                    'sort_order'   => 0,
                    'is_default'   => true,
                    'is_completed' => false,
                    'project_id'   => $project->id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Assign this status to all existing tasks in this project that don't have a status_id
                DB::table('tasks')
                    ->where('project_id', $project->id)
                    ->whereNull('status_id')
                    ->update(['status_id' => $statusId]);
            }
        }

        // Handle tasks that don't belong to any project
        $orphanTasks = DB::table('tasks')
            ->whereNull('project_id')
            ->whereNull('status_id')
            ->get();

        if ($orphanTasks->count() > 0) {
            // Create a default project for orphan tasks if needed
            // For now, we'll just leave them with status_id = null
            // They can be assigned statuses when they get assigned to projects later
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove default "To Do" statuses created by this migration
        DB::table('task_statuses')
            ->where('name', 'To Do')
            ->where('is_default', true)
            ->delete();
    }
};
