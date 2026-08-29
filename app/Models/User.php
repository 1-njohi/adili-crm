<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_permanent',
        'social_handles',
        'bank_name',
        'account_number',
        'account_holder_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_permanent' => 'boolean',
        'social_handles' => 'array',
    ];

    // Helper methods
    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    public function isOwner(): bool
    {
        return in_array($this->role, ['superadmin', 'manager']);
    }

    // Agent relationships
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'agent_project', 'agent_id', 'project_id')
            ->withPivot('commission_type', 'commission_rate', 'commission_currency', 'status')
            ->withTimestamps();
    }
    public function activeProjects()
    {
        return $this->projects()->wherePivot('status', 'active');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'agent_id');
    }

    public function leadTethers()
    {
        return $this->hasMany(LeadTether::class, 'agent_id');
    }

    // app/Models/User.php

    public function salesAsBuyer()
    {
        return $this->hasMany(Sale::class, 'buyer_id');
    }

    public function salesAsAgent()
    {
        return $this->hasMany(Sale::class, 'agent_id');
    }

    public function activeSalesAsBuyer()
    {
        return $this->salesAsBuyer()->where('status', 'active');
    }
}