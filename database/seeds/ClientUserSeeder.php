<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first client or create one if none exists
        $client = Client::first();
        if (!$client) {
            $client = Client::create([
                'name'        => 'Test Client',
                'email'       => 'test@client.com',
                'is_billable' => true,
            ]);
        }

        // Create a test client user
        ClientUser::create([
            'name'      => 'John Doe',
            'email'     => 'john@client.com',
            'password'  => Hash::make('password'),
            'client_id' => $client->id,
            'is_active' => true,
        ]);

        // Create another test client user
        ClientUser::create([
            'name'      => 'Jane Smith',
            'email'     => 'jane@client.com',
            'password'  => Hash::make('password'),
            'client_id' => $client->id,
            'is_active' => true,
        ]);
    }
}
