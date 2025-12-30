<?php

use Database\Seeders\ClientUserSeeder;
use Database\Seeders\FeaturesTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ClientsTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(FeaturesTableSeeder::class);
        $this->call(SprintsTableSeeder::class);
        $this->call(TasksTableSeeder::class);
        $this->call(SessionsTableSeeder::class);
        $this->call(InvoicesTableSeeder::class);
        $this->call(TargetsTableSeeder::class);
        $this->call(ClientUserSeeder::class);
    }
}
