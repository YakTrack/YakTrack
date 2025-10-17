<?php

namespace Database\Factories;

use App\Models\TestResult;
use App\EvidenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestResultEvidence>
 */
class TestResultEvidenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(EvidenceType::cases());

        return [
            'test_result_id' => TestResult::factory(),
            'type' => $type,
            'file_path' => $type === EvidenceType::Image ? 'test-evidence/' . $this->faker->uuid() . '.jpg' : null,
            'content' => $type === EvidenceType::Text ? $this->faker->paragraphs(2, true) : null,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate that the evidence is an image.
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EvidenceType::Image,
            'file_path' => 'test-evidence/' . $this->faker->uuid() . '.jpg',
            'content' => null,
        ]);
    }

    /**
     * Indicate that the evidence is text.
     */
    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EvidenceType::Text,
            'file_path' => null,
            'content' => $this->faker->paragraphs(2, true),
        ]);
    }

    /**
     * Indicate that the evidence has no sort order.
     */
    public function withoutSortOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => 0,
        ]);
    }
}