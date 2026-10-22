<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\RepairStatus;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $statuses = RepairStatus::withCount('tickets')->orderBy('sort_order')->get();
        $lowStock = Part::lowStock()->with('category')->orderBy('quantity')->get();
        $recent = Ticket::with(['customer', 'status'])->latest()->limit(8)->get();

        return view('dashboard', compact('statuses', 'lowStock', 'recent'));
    }
}
