<?php

use App\Models\Target;

it('can view the create target page', function () {
    $this->actingAsUser();

    $response = $this->get(route('target.create'));

    $response->assertSuccessful();
});

it('can create a daily target', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $response = $this->post(route('target.store', $targetDetails = [
        'duration'      => 1,
        'duration_unit' => Target::DURATION_UNITS['DAYS']['key'],
        'value'         => 8,
        'value_unit'    => Target::VALUE_UNITS['HOURS']['key'],
        'billable_only' => 1,
        'starts_at'     => '2020-01-01 00:00:00',
    ]));

    $response->assertRedirect(route('target.index'));

    $this->assertDatabaseHas('targets', $targetDetails);
});

it('cannot create a daily target for a date which already has one', function () {
    $this->actingAsUser();

    $existingTarget = Target::create($targetDetails = [
        'duration'      => 1,
        'duration_unit' => Target::DURATION_UNITS['DAYS']['key'],
        'value'         => 8,
        'value_unit'    => Target::VALUE_UNITS['HOURS']['key'],
        'billable_only' => 1,
        'starts_at'     => '2020-01-01 00:00:00',
    ]);

    $response = $this->post(route('target.store', $targetDetails));

    $response->assertRedirect();
    $response->assertSessionHasErrors();

    expect(Target::count())->toBe(1);
});
