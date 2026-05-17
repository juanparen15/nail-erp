<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'title',
        'message',
        'channels',
        'target',
        'status',
        'scheduled_at',
        'sent_at',
        'sent_count',
    ];

    protected $casts = [
        'channels' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'sent_count' => 'integer',
    ];

    public function getTargetLabelAttribute(): string
    {
        return match ($this->target) {
            'all' => 'Todos los clientes',
            'inactive_3months' => 'Inactivos +3 meses',
            'birthday_month' => 'Cumpleaños del mes',
            default => $this->target,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Borrador',
            'scheduled' => 'Programada',
            'sent' => 'Enviada',
            default => $this->status,
        };
    }

    public function getTargetClients()
    {
        return match ($this->target) {
            'all' => Client::all(),
            'inactive_3months' => Client::inactiveSince(3)->get(),
            default => collect(),
        };
    }
}
