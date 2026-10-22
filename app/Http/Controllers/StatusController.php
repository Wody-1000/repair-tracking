<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

// Public page: customers check their repair by ticket number. No login, and no personal or cost data is shown.
class StatusController extends Controller
{
    public function index(Request $request)
    {
        $ticket = null;
        $searched = false;

        if ($request->filled('ticket')) {
            $request->validate(['ticket' => ['string', 'max:20']]);
            $searched = true;
            $ticket = Ticket::with(['status', 'histories.status'])
                ->where('ticket_number', strtoupper(trim($request->string('ticket'))))
                ->first();
        }

        return view('status', compact('ticket', 'searched'));
    }
}
