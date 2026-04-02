<?php

use App\Enums\OrganizationRole;
use App\Models\Membership;
use App\Models\Organization;
use App\Models\User;

test('user can switch to an organization they belong to', function () {
    $user = User::factory()->create();
    $otherOrg = Organization::factory()->create();
    $user->organizations()->attach($otherOrg, [
        'id' => Membership::generatePrefixedUlid(),
        'role' => OrganizationRole::Member,
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('organization.switch', $otherOrg));

    $response->assertRedirect();

    expect($user->refresh()->current_organization_id)->toBe($otherOrg->id);
});

test('user cannot switch to an organization they do not belong to', function () {
    $user = User::factory()->create();
    $otherOrg = Organization::factory()->create();

    $this
        ->actingAs($user)
        ->put(route('organization.switch', $otherOrg));

    expect($user->refresh()->current_organization_id)->not->toBe($otherOrg->id);
});

test('switching organization requires authentication', function () {
    $org = Organization::factory()->create();

    $response = $this->put(route('organization.switch', $org));

    $response->assertRedirect(route('login'));
});

test('organizations are shared via inertia props', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('auth.organizations')
        ->has('auth.currentOrganization')
    );
});
