<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = ['user_id', 'date', 'check_in', 'check_out'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->check_out) return null;

        $checkIn = \Carbon\Carbon::parse($this->check_in);
        $checkOut = \Carbon\Carbon::parse($this->check_out);
        $diff = $checkIn->diff($checkOut);

        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }
        return $diff->i . 'm';
    }
}
