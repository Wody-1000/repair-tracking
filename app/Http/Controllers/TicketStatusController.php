<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Technician / admin work on a ticket: change status, assign technician, set labor cost.
class TicketStatusController extends Controller
{
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! $ticket->canBeWorkedOnBy($request->user())) {
            return back()->withErrors(['status_id' => 'This ticket is assigned to another technician.']);
        }

        $data = $request->validate([
            'status_id' => ['required', 'integer', 'exists:repair_statuses,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'labor_cost' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $ticket, $request) {
            $statusChanged = (int) $data['status_id'] !== $ticket->status_id;

            $ticket->update([
                'status_id' => $data['status_id'],
                'assigned_to' => $data['assigned_to'] ?? $ticket->assigned_to ?? $request->user()->id,
                'labor_cost' => $data['labor_cost'],
            ]);

            if ($statusChanged || ! empty($data['note'])) {
                TicketStatusHistory::create([
                    'ticket_id' => $ticket->id,
                    'status_id' => $data['status_id'],
                    'changed_by' => $request->user()->id,
                    'note' => $data['note'] ?? null,
                ]);
            }
        });

        return back()->with('success', 'Repair progress updated.');
    }
}
