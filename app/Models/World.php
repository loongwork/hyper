<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class World extends Model
{
    use HasFactory;
    use ReadOnlyTrait;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'rowid';
}
