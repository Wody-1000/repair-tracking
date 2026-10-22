<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Ticket;
use App\Models\TicketPart;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

// Using a part on a ticket. The database trigger does the stock deduction (and blocks over-use).
class TicketPartController extends Controller
{
    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $ticket->canBeWorkedOnBy($request->user())) {
            return back()->withErrors(['part_id' => 'This ticket is assigned to another technician.']);
        }

        if ($ticket->load('status')->isReleased()) {
            return back()->withErrors(['part_id' => 'This ticket is already released. Parts can no longer be added.']);
        }

        $data = $request->validate([
            'part_id' => ['required', 'integer', 'exists:parts,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $part = Part::findOrFail($data['part_id']);

        try {
            TicketPart::create([
                'ticket_id' => $ticket->id,
                'part_id' => $part->id,
                'quantity' => $data['quantity'],
                'unit_price' => $part->unit_price,
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Insufficient stock')) {
                return back()->withErrors(['quantity' => "Not enough stock for {$part->name}. Available: {$part->quantity}."])->withInput();
            }
            throw $e;
        }

        return back()->with('success', 'Part added. Stock was deducted automatically.');
    }

    public function destroy(Request $request, Ticket $ticket, TicketPart $ticketPart): RedirectResponse
    {
        if (! $ticket->canBeWorkedOnBy($request->user())) {
            return back()->withErrors(['part_id' => 'This ticket is assigned to another technician.']);
        }

        abort_unless($ticketPart->ticket_id === $ticket->id, 404);

        $ticketPart->delete(); // trigger trg_restore_part_stock gives the stock back

        return back()->with('success', 'Part removed. Stock was returned.');
    }
}
