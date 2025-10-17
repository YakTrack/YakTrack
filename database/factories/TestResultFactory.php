<?php

namespace Database\Factories;

use App\Models\AcceptanceCriteria;
use App\Models\AcceptanceCriteriaVersion;
use App\Models\TestRun;
use App\TestResultStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestResult>
 */
class TestResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'test_run_id'                    => TestRun::factory(),
            'acceptance_criteria_id'         => AcceptanceCriteria::factory(),
            'acceptance_criteria_version_id' => AcceptanceCriteriaVersion::factory(),
            'status'                         => $this->faker->randomElement(TestResultStatus::cases()),
            'notes'                          => $this->faker->optional(0.4)->paragraphs(1, true),
        ];
    }

    /**
     * Indicate that the test result passed.
     */
    public function passed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TestResultStatus::Passed,
        ]);
    }

    /**
     * Indicate that the test result failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TestResultStatus::Failed,
        ]);
    }

    /**
     * Indicate that the test result was skipped.
     */
    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TestResultStatus::Skipped,
        ]);
    }

    /**
     * Indicate that the test result was blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TestResultStatus::Blocked,
        ]);
    }

    /**
     * Indicate that the test result has no notes.
     */
    public function withoutNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => null,
        ]);
    }

    /**
     * Indicate that the test result has no version reference.
     */
    public function withoutVersion(): static
    {
        return $this->state(fn (array $attributes) => [
            'acceptance_criteria_version_id' => null,
        ]);
    }
}
