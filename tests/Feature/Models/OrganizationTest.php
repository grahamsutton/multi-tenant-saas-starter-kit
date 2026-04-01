<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\QueryException;

test('organization id has org_ prefix', function () {
    $organization = Organization::factory()->create();

    expect($organization->id)->toStartWith('org_');
});

test('organization has users relationship', function () {
    $user = User::factory()->create();

    expect($user->currentOrganization->users)->toHaveCount(1)
        ->and($user->currentOrganization->users->first()->id)->toBe($user->id);
});

test('organization slug must be unique', function () {
    Organization::factory()->create(['slug' => 'test-org']);

    expect(fn () => Organization::factory()->create(['slug' => 'test-org']))
        ->toThrow(QueryException::class);
});
