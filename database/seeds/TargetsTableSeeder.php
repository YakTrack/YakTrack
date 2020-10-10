<?php

use App\Models\Target;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TargetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Target::class)->states('for_date', 'in_hours')->create([
            'starts_at' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'value'     => 8,
        ]);
        factory(Target::class)->states('for_date', 'in_hours')->create([
            'starts_at' => Carbon::now()->format('Y-m-d'),
            'value'     => 8,
        ]);
        factory(Target::class)->states('for_date', 'in_hours')->create([
            'starts_at' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'value'     => 8,
        ]);
    }
}
