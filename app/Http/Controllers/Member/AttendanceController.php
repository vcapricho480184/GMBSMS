<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::where('user_id', auth()->id());

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
        }

        $attendance = $query->latest('date')->paginate(20)->withQueryString();

        return view('member.attendance.index', compact('attendance'));
    }

    public function checkIn()
    {
        $existing = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->whereNull('check_out')
            ->first();

        if ($existing) {
            return back()->with('error', 'You are already checked in.');
        }

        Attendance::create([
            'user_id' => auth()->id(),
            'date' => Carbon::today(),
            'check_in' => Carbon::now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Checked in successfully!');
    }

    public function checkOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->whereNull('check_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No active check-in found.');
        }

        $attendance->update(['check_out' => Carbon::now()->format('H:i:s')]);

        return back()->with('success', 'Checked out successfully!');
    }
}
