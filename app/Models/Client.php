<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'whatsapp',
        'email',
        'notes',
        'total_visits',
        'last_visit_at',
    ];

    protected $casts = [
        'last_visit_at' => 'datetime',
        'total_visits' => 'integer',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeInactiveSince($query, int $months)
    {
        return $query->where(function ($q) use ($months) {
            $q->whereNull('last_visit_at')
              ->orWhere('last_visit_at', '<', now()->subMonths($months));
        });
    }

    public function incrementVisits(): void
    {
        $this->increment('total_visits');
        $this->update(['last_visit_at' => now()]);
    }
}
