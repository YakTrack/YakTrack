<?php

use App\Models\AcceptanceCriteria;
use App\Models\Feature;

it('can soft delete a feature', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->delete(route('features.destroy', $feature));

    $response->assertRedirect(route('features.index'));
    $response->assertSessionHas('success', 'Feature deleted successfully.');

    $feature->refresh();
    expect($feature->is_active)->toBeFalse();
});

it('can delete a feature with linked acceptance criteria', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();
    AcceptanceCriteria::factory()->create(['feature_id' => $feature->id]);

    $response = $this->delete(route('features.destroy', $feature));

    $response->assertRedirect(route('features.index'));
    $response->assertSessionHas('success', 'Feature deleted successfully.');

    $feature->refresh();
    expect($feature->is_active)->toBeFalse();
});

it('preserves acceptance criteria when deleting feature', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create(['feature_id' => $feature->id]);

    $this->delete(route('features.destroy', $feature));

    $criteria->refresh();
    expect($criteria->feature_id)->toBe($feature->id);
    expect($criteria->is_active)->toBeTrue();
});

it('requires authentication to delete a feature', function () {
    $feature = Feature::factory()->create();

    $response = $this->delete(route('features.destroy', $feature));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent feature deletion', function () {
    $this->actingAsUser();

    $response = $this->delete(route('features.destroy', 999));

    $response->assertNotFound();
});

it('shows success message after deletion', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->delete(route('features.destroy', $feature));

    $response->assertSessionHas('success', 'Feature deleted successfully.');
});

it('can delete multiple features', function () {
    $this->actingAsUser();

    $feature1 = Feature::factory()->create();
    $feature2 = Feature::factory()->create();

    $this->delete(route('features.destroy', $feature1));
    $this->delete(route('features.destroy', $feature2));

    $feature1->refresh();
    $feature2->refresh();

    expect($feature1->is_active)->toBeFalse();
    expect($feature2->is_active)->toBeFalse();
});

it('does not appear in index after deletion', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $this->delete(route('features.destroy', $feature));

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 0)
    );
});

it('can still be accessed directly after soft deletion', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $this->delete(route('features.destroy', $feature));

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Features/Show')
        ->has('feature')
    );
});
