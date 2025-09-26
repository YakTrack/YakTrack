<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date'      => Carbon::today()->format('Y-m-d H:i:s'),
            'due_date'  => Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
            'number'    => $this->generateUniqueInvoiceNumber(),
            'client_id' => Client::factory(),
        ];
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateUniqueInvoiceNumber(): string
    {
        do {
            $invoiceNumber = strtoupper($this->faker->word).'-'.$this->faker->randomNumber(3);
        } while (Invoice::where('number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }
}
