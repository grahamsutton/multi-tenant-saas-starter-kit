<?php

use App\Enums\OrganizationRole;
use App\Models\Membership;
use App\Models\Organization;
use App\Models\User;

test('user factory creates a personal organization', function () {
    $user = User::factory()->create();

    expect($user->id)->toStartWith('usr_')
        ->and($user->current_organization_id)->not->toBeNull()
        ->and($user->currentOrganization->personal)->toBeTrue()
        ->and($user->organizations)->toHaveCount(1);
});

test('user can belong to multiple organizations', function () {
    $user = User::factory()->create();
    $secondOrg = Organization::factory()->create();

    $user->organizations()->attach($secondOrg, [
        'id' => Membership::generatePrefixedUlid(),
        'role' => OrganizationRole::Member,
    ]);

    expect($user->fresh()->organizations)->toHaveCount(2);
});

test('user can switch to an organization they belong to', function () {
    $user = User::factory()->create();
    $secondOrg = Organization::factory()->create();

    $user->organizations()->attach($secondOrg, [
        'id' => Membership::generatePrefixedUlid(),
        'role' => OrganizationRole::Member,
    ]);

    $user->switchOrganization($secondOrg);

    expect($user->fresh()->current_organization_id)->toBe($secondOrg->id);
});

test('user cannot switch to an organization they do not belong to', function () {
    $user = User::factory()->create();
    $otherOrg = Organization::factory()->create();

    expect(fn () => $user->switchOrganization($otherOrg))
        ->toThrow(InvalidArgumentException::class, 'User is not a member of this organization.');
});

test('membership id has mem_ prefix', function () {
    $user = User::factory()->create();

    $membership = Membership::where('user_id', $user->id)->first();

    expect($membership->id)->toStartWith('mem_');
});
