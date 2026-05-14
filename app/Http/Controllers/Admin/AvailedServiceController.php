<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailedService;
use App\Models\GymService;
use App\Models\User;
use App\Models\BillingTransaction;
use Illuminate\Http\Request;

class AvailedServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = AvailedService::with(['user', 'gymService', 'approvedBy'])->approved(); // Only show approved services

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('service')) {
            $query->where('gym_service_id', $request->service);
        }

        $availedServices = $query->latest('approved_at')->paginate(15)->withQueryString();
        $services = GymService::all();

        return view('admin.availed-services.index', compact('availedServices', 'services'));
    }
}
