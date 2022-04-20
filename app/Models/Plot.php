<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

/**
 * @mixin IdeHelperPlot
 */
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
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(PlayerProfile::class, 'owner', 'uuid');
    }

    /**
     * Get the plot world relationship.
     */
    public function plotWorld(): BelongsTo
    {
        return $this->belongsTo(World::class, 'world', 'world');
    }
}
