<?php

use App\Models\User;

test('organization page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('organization.edit'));

    $response->assertOk();
});

test('organization name can be updated', function () {
    $user = User::factory()->create();
    $organization = $user->currentOrganization;
    $oldSlug = $organization->slug;

    $response = $this
        ->actingAs($user)
        ->patch(route('organization.update'), [
            'name' => 'Updated Organization',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('organization.edit'));

    $organization->refresh();

    expect($organization->name)->toBe('Updated Organization');
    expect($organization->slug)->not->toBe($oldSlug);
});

test('organization name is required', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('organization.update'), [
            'name' => '',
        ]);

    $response->assertSessionHasErrors('name');
});

test('organization page requires authentication', function () {
    $response = $this->get(route('organization.edit'));

    $response->assertRedirect(route('login'));
});
