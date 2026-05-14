<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'duration_days', 'status'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_days == 30) return '1 Month';
        if ($this->duration_days == 90) return '3 Months';
        if ($this->duration_days == 180) return '6 Months';
        if ($this->duration_days == 365) return '1 Year';
        return $this->duration_days . ' Days';
    }
}
