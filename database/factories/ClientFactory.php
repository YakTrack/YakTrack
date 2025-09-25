<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'is_billable' => true,
        ];
    }

    /**
     * Indicate that the client should have invoices.
     */
    public function withInvoices(): static
    {
        return $this->afterCreating(function (Client $client) {
            Invoice::factory(10)->create(['client_id' => $client->id]);
        });
    }
}
