<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTether extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'project_id',
        'phone_hash',
        'phone_encrypted',
        'email_hash',
        'email_encrypted',
        'name',
        'source',
        'source_platform',
        'attribution_data',
        'fingerprint',
        'tethered_at',
        'expires_at',
        'attribution_conflict',
        'notes',
    ];

    protected $casts = [
        'attribution_data' => 'array',
        'tethered_at' => 'datetime',
        'expires_at' => 'datetime',
        'attribution_conflict' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function behavior()
    {
        return $this->hasMany(LeadBehavior::class);
    }

    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function softHolds()
    {
        return $this->hasMany(SoftHold::class);
    }

    public function isActive()
    {
        return $this->expires_at->isFuture();
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}