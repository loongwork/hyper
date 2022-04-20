<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class Plot extends Model
{
    use HasFactory;
    use ReadOnlyTrait;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the player relationship.
     *
     * @return BelongsTo
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(PlayerProfile::class, 'owner', 'uuid');
    }

    /**
     * Get the plot world relationship.
     *
     * @return BelongsTo
     */
    public function plotWorld(): BelongsTo
    {
        return $this->belongsTo(World::class, 'world', 'world');
    }
}
