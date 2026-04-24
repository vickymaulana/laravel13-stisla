<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Activity log entry.
 *
 * Records user and system events such as login, logout, profile updates,
 * CRUD operations, and custom actions.  Each entry optionally references
 * the related Eloquent model via a polymorphic relationship.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $subject
 * @property string $description
 * @property string $event
 * @property string|null $model_type
 * @property int|null $model_id
 * @property array|null $properties
 * @property string|null $ip_address
 * @property string|null $user_agent
 */
class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'event',
        'model_type',
        'model_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * Get the user that owns the activity log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log a general activity.
     */
    public static function log(string $description, ?string $subject = null, string $event = 'custom', $model = null, array $properties = []): static
    {
        return static::create([
            'user_id' => auth()->id(),
            'subject' => $subject,
            'description' => $description,
            'event' => $event,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log user login.
     */
    public static function logLogin($user): static
    {
        return static::create([
            'user_id' => $user->id,
            'subject' => 'User Login',
            'description' => $user->name.' logged into the system',
            'event' => 'login',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log user logout.
     */
    public static function logLogout($user): static
    {
        return static::create([
            'user_id' => $user->id,
            'subject' => 'User Logout',
            'description' => $user->name.' logged out from the system',
            'event' => 'logout',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log profile update.
     */
    public static function logProfileUpdate($user): static
    {
        return static::create([
            'user_id' => $user->id,
            'subject' => 'Profile Updated',
            'description' => $user->name.' updated their profile',
            'event' => 'updated',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log password change.
     */
    public static function logPasswordChange($user): static
    {
        return static::create([
            'user_id' => $user->id,
            'subject' => 'Password Changed',
            'description' => $user->name.' changed their password',
            'event' => 'updated',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
