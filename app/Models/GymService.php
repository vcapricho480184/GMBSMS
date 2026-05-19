<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymService extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'status'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function availedServices()
    {
        return $this->hasMany(AvailedService::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format((float) $this->price, 2);
    }
}
