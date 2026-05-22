<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'membership_plan_id', 'start_date', 'end_date', 'status', 'billed'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'billed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    public function scopeUnbilled($query)
    {
        return $query->where('billed', false);
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'active') return 0;
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->status === 'active' && $this->days_remaining <= 7 && $this->days_remaining > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
                     ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays($days)]);
    }
}
