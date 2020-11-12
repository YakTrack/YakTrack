<?php

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
        $acme = factory(App\Models\Client::class)->states('with_invoices')->create([
            'name' => 'Acme Inc.',
        ]);
        $bertrandCarroll = factory(App\Models\Client::class)->states('with_invoices')->create([
            'name' => 'Bertrand Carroll',
        ]);
        $edesapaAndSongs = factory(App\Models\Client::class)->states('with_invoices')->create([
            'name' => 'Edesapa and Sons',
        ]); 
    }
}
