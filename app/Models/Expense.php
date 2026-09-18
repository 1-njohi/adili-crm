<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'created_by',
        'amount',
        'currency',
        'category',
        'description',
        'incurred_at',
        'receipt_path',
        'internal_notes',
        'type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'incurred_at' => 'date',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function isPreLaunch(): bool
    {
        return $this->type === 'pre_launch';
    }

    public function isPostLaunch(): bool
    {
        return $this->type === 'post_launch';
    }
}
