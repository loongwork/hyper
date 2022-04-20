<?php

namespace App\Models;

use App\Enums\HashAlgorithm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\readonly\ReadOnlyTrait;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\HasManyJson;

class PlayerProfile extends Model
{
    use HasFactory;
    use ReadOnlyTrait;
    use HasJsonRelationships;

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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the user relationship.
     *
     * @return HasManyJson
     */
    public function user(): HasManyJson
    {
        return $this->hasManyJson(User::class, 'profile_ids')->limit(1);
    }
}
