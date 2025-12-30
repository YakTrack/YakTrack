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
        $projectNames = [
            'Website Redesign',
            'Mobile App Development',
            'API Integration',
            'E-commerce Platform',
            'Dashboard Analytics',
            'Content Management System',
            'Customer Portal',
            'Payment Gateway',
            'Data Migration',
        ];

        $projectIndex = 0;
        Client::all()->each(function ($client) use (&$projectIndex, $projectNames) {
            for ($i = 0; $i < 3; $i++) {
                Project::factory()->create([
                    'client_id' => $client->id,
                    'name'      => $projectNames[$projectIndex],
                ]);
                $projectIndex++;
            }
        });
    }
}
