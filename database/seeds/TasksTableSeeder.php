<?php

use Illuminate\Database\Seeder;

use App\Sprint;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sprint::all()->each( function($sprint) {
            factory(App\Task::class, 3)->create([
                'project_id' => $sprint->project->id,
                'sprint_id' => $sprint->id
            ]);
        });
    }
}
