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
    public function run(): void
    {
        Project::all()->each(function (Project $project) {
            foreach (range(1, 3) as $i) {
                $sprint = Sprint::create([
                    'name'    => $project->name.' - Sprint '.$i,
                    'is_open' => $i === 1,
                ]);
                $sprint->projects()->attach($project->id);
            }
        });
    }
}
