<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailedService extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'gym_service_id', 'availed_date', 'notes', 'status', 'admin_notes', 'approved_by', 'approved_at'];

    protected $casts = [
        'availed_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gymService()
    {
        return $this->belongsTo(GymService::class);
    }

    public function billingItems()
    {
        return $this->hasMany(BillingTransactionItem::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
