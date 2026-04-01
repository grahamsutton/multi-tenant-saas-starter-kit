<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use RuntimeException;

trait HasPrefixedUlid
{
    /**
     * Models MUST define this property with a 3-letter lowercase prefix.
     *
     * Example:
     *   protected static string $ulidPrefix = 'usr';
     */

    /**
     * Boot the trait.
     */
    protected static function bootHasPrefixedUlid(): void
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->id = static::generatePrefixedUlid();
            }
        });
    }

    /**
     * Initialize the trait.
     *
     * Disable auto-incrementing and set the ID type to string.
     * Automatically executed when the trait is registered on the model.
     */
    public function initializeHasPrefixedUlid(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    /**
     * Generate a new ULID with the model's prefix.
     */
    public static function generatePrefixedUlid(): string
    {
        return Str::lower(static::resolveUlidPrefix().'_'.(string) Str::ulid());
    }

    /**
     * Resolve the ULID prefix (STRICT POLICY A).
     *
     * Every model MUST define:
     *   protected static string $ulidPrefix = 'abc';
     *
     * Where the prefix is exactly 3 lowercase letters.
     *
     * @throws RuntimeException
     */
    protected static function resolveUlidPrefix(): string
    {
        if (! property_exists(static::class, 'ulidPrefix')) {
            throw new RuntimeException(sprintf(
                '%s must define a valid ULID prefix: protected static string $ulidPrefix = \'abc\'; (exactly 3 lowercase letters).',
                static::class
            ));
        }

        $prefix = static::$ulidPrefix;

        if (! is_string($prefix) || preg_match('/^[a-z]{3}$/', $prefix) !== 1) {
            throw new RuntimeException(sprintf(
                '%s must define a valid ULID prefix: protected static string $ulidPrefix = \'abc\'; (exactly 3 lowercase letters).',
                static::class
            ));
        }

        return $prefix;
    }
}
