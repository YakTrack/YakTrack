<?php

use App\Models\User;

it('requires authentication to view tokens', function () {
    $this->get(route('api-tokens.index'))
        ->assertRedirect(route('login'));
});

it('can view the token index page', function () {
    $this->actingAsUser();

    $this->get(route('api-tokens.index'))
        ->assertSuccessful()
        ->assertHasProp('tokens');
});

it('lists only the authenticated user tokens', function () {
    $user = factory(User::class)->create();
    $otherUser = factory(User::class)->create();

    $user->createToken('My Token');
    $otherUser->createToken('Other Token');

    $this->actingAs($user);

    $response = $this->get(route('api-tokens.index'));
    $response->assertSuccessful();

    $tokens = $response->props('tokens');
    expect($tokens)->toHaveCount(1);
    expect($tokens[0]['name'])->toBe('My Token');
});

it('can view the create token page', function () {
    $this->actingAsUser();

    $this->get(route('api-tokens.create'))
        ->assertSuccessful();
});

it('can create a token', function () {
    $user = $this->actingAsUser();

    $response = $this->post(route('api-tokens.store'), [
        'name' => 'CLI Token',
    ]);

    $response->assertRedirect(route('api-tokens.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('personal_access_tokens', [
        'name'           => 'CLI Token',
        'tokenable_id'   => $user->id,
        'tokenable_type' => User::class,
    ]);
});

it('returns the plain text token in the newToken flash key', function () {
    $this->actingAsUser();

    $this->post(route('api-tokens.store'), [
        'name' => 'Flash Token',
    ]);

    $newToken = session('newToken');
    expect($newToken)->not->toBeNull();
    expect($newToken)->toContain('|');
});

it('validates token name is required', function () {
    $this->actingAsUser();

    $this->post(route('api-tokens.store'), [
        'name' => '',
    ])->assertSessionHasErrors(['name']);
});

it('validates token name max length', function () {
    $this->actingAsUser();

    $this->post(route('api-tokens.store'), [
        'name' => str_repeat('a', 256),
    ])->assertSessionHasErrors(['name']);
});

it('can revoke a token', function () {
    $user = $this->actingAsUser();
    $token = $user->createToken('Revoke Me');

    $this->delete(route('api-tokens.destroy', $token->accessToken->id))
        ->assertRedirect(route('api-tokens.index'))
        ->assertSessionHas('success', 'Token revoked.');

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
});

it('shows token details in the list', function () {
    $user = $this->actingAsUser();
    $user->createToken('Detail Token');

    $response = $this->get(route('api-tokens.index'));
    $tokens = $response->props('tokens');

    expect($tokens[0])->toHaveKeys(['id', 'name', 'last_used_at', 'created_at']);
    expect($tokens[0]['name'])->toBe('Detail Token');
});
