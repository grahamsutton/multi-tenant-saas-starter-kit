<?php

use App\Enums\OrganizationRole;
use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'organization_name' => 'Acme Inc.',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new users receive an organization on registration', function () {
    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'organization_name' => 'Acme Inc.',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    expect($user->id)->toStartWith('usr_')
        ->and($user->current_organization_id)->not->toBeNull()
        ->and($user->currentOrganization->id)->toStartWith('org_')
        ->and($user->currentOrganization->name)->toBe('Acme Inc.')
        ->and($user->organizations()->first()->pivot->role)->toBe(OrganizationRole::Owner);
});

test('organization name is required at registration', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('organization_name');
});
