<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name'      => 'Test User',
            'email'     => 'user@domain.com',
            'password'  => bcrypt('password'),
        ]);
    }
}
