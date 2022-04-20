<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * @mixin IdeHelperVerificationCode
 */
class VerificationCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['code', 'verifiable', 'expires_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['code'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Create a verification code for the verifiable.
     */
    public static function createFor(string $verifiable): string
    {
        $character = '0123456789ABCDEFGHJKMNPQRSTUVWXYZ';

        $code = collect(range(0, 7))
            ->map(static function () use ($character) {
                return $character[random_int(0, strlen($character) - 1)];
            })
            ->join('');

        self::query()->create([
            'code' => $code,
            'verifiable' => $verifiable,
        ]);

        return $code;
    }

    /**
     * Verify the code.
     */
    public static function verify(string $verifiable, string $code): bool
    {
        $verification_code = self::for($verifiable)
            ->notExpired()
            ->cursor()
            ->contains(static function (VerificationCode $verification_code) use ($code) {
                return Hash::check($code, $verification_code->code);
            });

        if (!$verification_code) {
            return false;
        }

        self::for($verifiable)->delete();

        return true;
    }

    /**
     * Scope a query to only include verification codes for the provided verifiable.
     */
    public function scopeFor(Builder $query, string $verifiable): Builder
    {
        return $query->where('verifiable', $verifiable);
    }

    /**
     * Scope a query to only include verification codes that have not expired.
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>=', now());
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (VerificationCode $model) {
            if ($model->expires_at === null) {
                $model->expires_at = now()->addMinutes(15);
            }

            if (Hash::needsRehash($model->code)) {
                $model->code = Hash::make($model->code);
            }
        });

        self::created(static function (VerificationCode $model) {
            $old_ids = self::for($model->verifiable)
                ->where('id', '<>', $model->id)
                ->pluck('id');

            self::query()->whereIn('id', $old_ids)->delete();
        });
    }
}
