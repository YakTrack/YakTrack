<?php

use App\Models\Session;
use App\Models\SessionCategory;
use Illuminate\Database\Seeder;

class SessionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Session::factory(100)->classified()->create();
        SessionCategory::factory(3)->create();
    }
}
