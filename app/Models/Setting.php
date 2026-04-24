<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Application setting stored in the database.
 *
 * Settings are grouped by category (general, email, social, appearance)
 * and cached individually to avoid repeated queries.
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property string $label
 * @property string|null $description
 * @property int|null $order
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'order',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value, string $type = 'text'): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Get all settings grouped by their group column.
     *
     * @return Collection
     */
    public static function getAllGrouped()
    {
        return static::orderBy('group')->orderBy('order')->get()->groupBy('group');
    }

    /**
     * Cast a raw value based on its declared type.
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (int) $value : $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Clear all cached settings.
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }
}
