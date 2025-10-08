<?php

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
        Sprint::all()->each(function ($sprint) {
            Task::factory(3)->create([
                'project_id' => $sprint->project->id,
            ]);
        });
    }
}
