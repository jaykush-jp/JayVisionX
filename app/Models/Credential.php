<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Encrypted store for third-party API credentials (AI provider keys, Google &
 * Meta app credentials, tokens). The value column is encrypted via the cast, so
 * secrets are never stored in plain text and never live in .env.
 *
 * Usage:
 *   Credential::get('ai.gemini.key')
 *   Credential::set('ai.gemini.key', $key)   // empty/null deletes the row
 *   Credential::has('google.client_id')
 */
class Credential extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'encrypted',
    ];

    /** Per-request cache of decrypted values (keyed by credential key). */
    private static array $cache = [];

    public static function get(string $key, $default = null): ?string
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key] ?? $default;
        }

        $row = static::where('key', $key)->first();
        $value = $row?->value;

        self::$cache[$key] = $value;

        return ($value === null || $value === '') ? $default : $value;
    }

    /** Save a value, or delete the row when the value is empty/null. */
    public static function set(string $key, ?string $value): void
    {
        $value = $value !== null ? trim($value) : null;

        if ($value === null || $value === '') {
            static::where('key', $key)->delete();
            self::$cache[$key] = null;
            return;
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
        self::$cache[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    /** Forget the in-memory cache (useful in tests / after bulk writes). */
    public static function flushCache(): void
    {
        self::$cache = [];
    }
}
