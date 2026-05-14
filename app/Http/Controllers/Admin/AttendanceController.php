<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $attendance = $query->latest('date')->latest('check_in')->paginate(20)->withQueryString();
        $todayCount = Attendance::whereDate('date', Carbon::today())->count();
        $checkedInNow = Attendance::whereDate('date', Carbon::today())->whereNull('check_out')->count();

        return view('admin.attendance.index', compact('attendance', 'todayCount', 'checkedInNow'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $existing = Attendance::where('user_id', $request->user_id)
            ->whereDate('date', Carbon::today())
            ->whereNull('check_out')
            ->first();

        if ($existing) {
            return back()->with('error', 'Member is already checked in.');
        }

        Attendance::create([
            'user_id' => $request->user_id,
            'date' => Carbon::today(),
            'check_in' => Carbon::now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Check-in recorded successfully.');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $attendance = Attendance::where('user_id', $request->user_id)
            ->whereDate('date', Carbon::today())
            ->whereNull('check_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No active check-in found for this member.');
        }

        $attendance->update(['check_out' => Carbon::now()->format('H:i:s')]);

        return back()->with('success', 'Check-out recorded successfully.');
    }
}
