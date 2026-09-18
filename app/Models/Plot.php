<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'plot_number',
        'size',
        'size_unit',
        'payment_tiers',
        'custom_attributes',
        'status',
    ];

    protected $casts = [
        'payment_tiers' => 'array',
        'custom_attributes' => 'array',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function hasActiveSale(): bool
    {
        return $this->sale()->where('status', 'active')->exists();
    }

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    public function getPriceForTier(string $tier): ?float
    {
        if (! $this->payment_tiers) {
            return null;
        }

        foreach ($this->payment_tiers as $t) {
            if ($t['tier'] === $tier) {
                return $t['price'];
            }
        }

        return null;
    }
}
