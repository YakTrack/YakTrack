<?php

use Illuminate\Database\Seeder;

use App\Project;

class SprintsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::all()->each( function($project) {
            factory(App\Sprint::class, 3)->create(['project_id' => $project->id]);
        });
    }
}
