<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftHold extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'lead_tether_id',
        'site_visit_id',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function leadTether()
    {
        return $this->belongsTo(LeadTether::class);
    }

    public function siteVisit()
    {
        return $this->belongsTo(SiteVisit::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
