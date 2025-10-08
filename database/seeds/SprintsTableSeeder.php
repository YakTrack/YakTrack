<?php

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Database\Seeder;

class SprintsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::all()->each(function ($project) {
            Sprint::factory(3)->create(['project_id' => $project->id]);
        });
    }
}
