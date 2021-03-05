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
        factory(Session::class, 100)->states('classified')->create();
        factory(SessionCategory::class, 3)->create();
    }
}
