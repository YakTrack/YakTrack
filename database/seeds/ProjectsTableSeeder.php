<?php

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::all()->each(function ($client) {
            Project::factory(3)->create(['client_id' => $client->id]);
        });
    }
}
