<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\HashAlgorithm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * @mixin IdeHelperPlayerProfile
 */
class PlayerProfile extends Model
{
    use HasFactory;
    use ReadOnlyTrait;
    use HasJsonRelationships;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'players';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['ip_address', 'password'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hashing_algorithm' => HashAlgorithm::class,
        'last_login_at' => 'datetime',
        'register_date' => 'date',
    ];

    /**
     * Get the user relationship.
     */
    public function user(): HasMany
    {
        return $this->hasManyJson(User::class, 'profile_ids')->limit(1);
    }

    /**
     * Get the plots relationship.
     */
    public function plots(): HasMany
    {
        return $this->hasMany(Plot::class, 'owner', 'uuid');
    }
}
