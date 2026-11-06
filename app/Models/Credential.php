<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Encrypted store for third-party API credentials (AI provider keys, Google &
 * Meta app credentials, tokens). The value column is encrypted via the cast, so
 * secrets are never stored in plain text and never live in .env.
 *
 * Each credential also has an `is_active` flag: an inactive credential keeps its
 * stored value but is treated as "not configured" by the app (get() returns the
 * default), so admins can disable an API without deleting the key.
 *
 * Usage:
 *   Credential::get('ai.gemini.key')          // active value, else default
 *   Credential::set('ai.gemini.key', $key)    // empty/null deletes the row
 *   Credential::rawHas('ai.gemini.key')        // value exists (even if inactive)
 *   Credential::isActive('ai.gemini.key')      // enabled flag (default true)
 *   Credential::setActive('ai.gemini.key', false)
 */
class Credential extends Model
{
    protected $fillable = ['key', 'value', 'is_active'];

    protected $casts = [
        'value'     => 'encrypted',
        'is_active' => 'boolean',
    ];

    /** Per-request cache: key => ['value'=>?string,'active'=>bool] | false (absent). */
    private static array $cache = [];

    private static function row(string $key)
    {
        if (! array_key_exists($key, self::$cache)) {
            $row = static::where('key', $key)->first();
            self::$cache[$key] = $row
                ? ['value' => $row->value, 'active' => (bool) $row->is_active]
                : false;
        }

        return self::$cache[$key];
    }

    /** The value if it exists AND is active, otherwise the default. */
    public static function get(string $key, $default = null): ?string
    {
        $r = self::row($key);
        if ($r === false || ! $r['active'] || $r['value'] === null || $r['value'] === '') {
            return $default;
        }

        return $r['value'];
    }

    /** True when a usable (active, non-empty) value exists. */
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    /** True when a value is stored, regardless of the active flag (for the UI badge). */
    public static function rawHas(string $key): bool
    {
        $r = self::row($key);

        return $r !== false && $r['value'] !== null && $r['value'] !== '';
    }

    /** The stored value regardless of the active flag (for editing in the admin UI). */
    public static function rawValue(string $key, $default = null): ?string
    {
        $r = self::row($key);

        return ($r === false || $r['value'] === null || $r['value'] === '') ? $default : $r['value'];
    }

    /** The enabled flag — defaults to true when no row exists yet. */
    public static function isActive(string $key): bool
    {
        $r = self::row($key);

        return $r === false ? true : $r['active'];
    }

    /** Save a value, or delete the row when the value is empty/null. New rows start active. */
    public static function set(string $key, ?string $value): void
    {
        $value = $value !== null ? trim($value) : null;

        if ($value === null || $value === '') {
            static::where('key', $key)->delete();
            unset(self::$cache[$key]);
            return;
        }

        $row = static::firstOrNew(['key' => $key]);
        $row->value = $value;
        if (! $row->exists) {
            $row->is_active = true;
        }
        $row->save();

        self::$cache[$key] = ['value' => $value, 'active' => (bool) $row->is_active];
    }

    /** Toggle a credential on/off without changing its value. No-op if it doesn't exist. */
    public static function setActive(string $key, bool $active): void
    {
        $row = static::where('key', $key)->first();
        if (! $row) {
            return;
        }

        $row->is_active = $active;
        $row->save();

        if (isset(self::$cache[$key]) && self::$cache[$key] !== false) {
            self::$cache[$key]['active'] = $active;
        }
    }

    /** Forget the in-memory cache (useful in tests / after bulk writes). */
    public static function flushCache(): void
    {
        self::$cache = [];
    }
}
