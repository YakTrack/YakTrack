<?php

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $acme = Client::factory()->withInvoices()->create([
            'name' => 'Acme Inc.',
        ]);
        $bertrandCarroll = Client::factory()->withInvoices()->create([
            'name' => 'Bertrand Carroll',
        ]);
        $edesapaAndSongs = Client::factory()->withInvoices()->create([
            'name' => 'Edesapa and Sons',
        ]);
    }
}
