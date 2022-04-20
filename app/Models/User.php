<?php

declare(strict_types=1);

namespace App\Models;

use Hidehalo\Nanoid\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasJsonRelationships;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'profile_ids' => 'array',
        'activated_at' => 'datetime',
        'become_member_at' => 'datetime',
        'whitelisted_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the profile relationship.
     */
    public function profiles(): BelongsToJson
    {
        return $this->belongsToJson(PlayerProfile::class, 'profile_ids');
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        self::creating(static function (self $user) {
            $user->{$user->getKeyName()} = (new Client())->generateId(21, Client::MODE_DYNAMIC);
        });
    }
}
