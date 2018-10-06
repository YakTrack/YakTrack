<?php

use App\Models\Project;
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
            factory(App\Models\Sprint::class, 3)->create(['project_id' => $project->id]);
        });
    }
}
