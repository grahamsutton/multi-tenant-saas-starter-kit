<?php

use App\Models\Concerns\HasPrefixedUlid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

test('generates prefixed ulid with correct format', function () {
    $ulid = User::generatePrefixedUlid();

    expect($ulid)->toStartWith('usr_')
        ->and(strlen($ulid))->toBe(30); // 3 prefix + 1 underscore + 26 ulid
});

test('generates unique ulids', function () {
    $first = User::generatePrefixedUlid();
    $second = User::generatePrefixedUlid();

    expect($first)->not->toBe($second);
});

test('throws for missing prefix', function () {
    expect(fn () => ModelWithoutPrefix::generatePrefixedUlid())
        ->toThrow(RuntimeException::class);
});

test('model has correct key configuration', function () {
    $user = new User;

    expect($user->getIncrementing())->toBeFalse()
        ->and($user->getKeyType())->toBe('string');
});

class ModelWithoutPrefix extends Model
{
    use HasPrefixedUlid;
}
