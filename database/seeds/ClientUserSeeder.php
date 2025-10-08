<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientUser;
use Illuminate\Database\Seeder;

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
            $client = Client::factory()->create([
                'name'        => 'Test Client',
                'email'       => 'test@client.com',
                'is_billable' => true,
            ]);
        }

        // Create test client users
        ClientUser::factory()->create([
            'name'      => 'John Doe',
            'email'     => 'john@client.com',
            'password'  => bcrypt('password'),
            'client_id' => $client->id,
            'is_active' => true,
        ]);

        ClientUser::factory()->create([
            'name'      => 'Jane Smith',
            'email'     => 'jane@client.com',
            'password'  => bcrypt('password'),
            'client_id' => $client->id,
            'is_active' => true,
        ]);
    }
}
