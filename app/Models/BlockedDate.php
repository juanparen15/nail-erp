<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedDate extends Model
{
    protected $fillable = [
        'date',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public static function isBlocked(string $date): bool
    {
        return static::where('date', $date)->exists();
    }
}
