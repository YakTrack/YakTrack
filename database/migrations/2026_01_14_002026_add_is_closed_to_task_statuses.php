<?php

use App\Models\Project;
use App\Models\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_statuses', function (Blueprint $table) {
            $table->boolean('is_closed')->default(false)->after('is_completed');
        });

        // Add 'Closed' status to all existing projects
        Project::query()->chunk(100, function ($projects) {
            foreach ($projects as $project) {
                // Skip if project already has a 'Closed' status
                if (TaskStatus::where('project_id', $project->id)->where('name', 'Closed')->exists()) {
                    continue;
                }

                // Get max sort_order for this project
                $maxSortOrder = TaskStatus::where('project_id', $project->id)->max('sort_order') ?? 0;

                TaskStatus::create([
                    'project_id'   => $project->id,
                    'name'         => 'Closed',
                    'color'        => '#8b5cf6',
                    'sort_order'   => $maxSortOrder + 1,
                    'is_default'   => false,
                    'is_completed' => false,
                    'is_closed'    => true,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'Closed' statuses created by this migration
        TaskStatus::where('name', 'Closed')
            ->where('is_closed', true)
            ->whereDoesntHave('tasks')
            ->delete();

        Schema::table('task_statuses', function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });
    }
};
