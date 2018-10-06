<?php

use App\Models\Sprint;
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
            factory(App\Models\Task::class, 3)->create([
                'project_id' => $sprint->project->id,
                'sprint_id'  => $sprint->id,
            ]);
        });
    }
}
