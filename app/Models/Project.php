<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'land_size',
        'land_size_unit',
        'status',
        'neighbor_discount',
        'primary_image',
        'gallery_images',
        // New microsite fields
        'meta_title',
        'meta_description',
        'hero_description',
        'hero_description_2',
        'drone_video_id',
        'brochure_path',
        'starting_price',
        'plot_size',
        'min_deposit',
        'max_months',
        'payment_tiers',
        'feature_groups',
        'amenities',
    ];

    protected $casts = [
        'neighbor_discount' => 'array',
        'gallery_images' => 'array',
        'payment_tiers' => 'array',
        'feature_groups' => 'array',
        'amenities' => 'array',
    ];

    // Relationships
    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function leadTethers()
    {
        return $this->hasMany(LeadTether::class);
    }

    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function marketingMaterials()
    {
        return $this->hasMany(MarketingMaterial::class);
    }

    // Helper methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSoldOut(): bool
    {
        return $this->status === 'sold_out';
    }

    public function totalPlots(): int
    {
        return $this->plots()->count();
    }

    public function availablePlots(): int
    {
        return $this->plots()->where('status', 'available')->count();
    }

    public function soldPlots(): int
    {
        return $this->plots()->where('status', 'sold')->count();
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'agent_project', 'project_id', 'agent_id')
            ->withPivot('commission_type', 'commission_rate', 'commission_currency', 'status')
            ->withTimestamps();
    }

    public function activeAgents()
    {
        return $this->agents()->wherePivot('status', 'active');
    }
}
