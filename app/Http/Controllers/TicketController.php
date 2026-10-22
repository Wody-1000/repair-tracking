<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Customer;
use App\Models\Part;
use App\Models\RepairStatus;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $statuses = RepairStatus::orderBy('sort_order')->get();

        $tickets = Ticket::with(['customer', 'status', 'technician'])
            ->search($request->query('q'))
            ->when($request->filled('status'), fn ($q) => $q->where('status_id', $request->integer('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact('tickets', 'statuses'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(TicketRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $ticket = DB::transaction(function () use ($data, $request) {
            $customer = Customer::firstOrCreate(
                ['name' => $data['customer_name'], 'phone' => $data['customer_phone']],
                ['email' => $data['customer_email'] ?? null],
            );

            $received = RepairStatus::where('name', 'Received')->firstOrFail();

            $ticket = Ticket::create([
                'ticket_number' => Ticket::nextNumber(),
                'customer_id' => $customer->id,
                'device_type' => $data['device_type'],
                'brand' => $data['brand'],
                'model' => $data['model'] ?? null,
                'serial_number' => $data['serial_number'] ?? null,
                'problem_description' => $data['problem_description'],
                'status_id' => $received->id,
                'created_by' => $request->user()->id,
            ]);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'status_id' => $received->id,
                'changed_by' => $request->user()->id,
                'note' => 'Device received',
            ]);

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} created. Give this number to the customer.");
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'status', 'creator', 'technician', 'parts.part', 'histories.status', 'histories.changedBy']);

        return view('tickets.show', [
            'ticket' => $ticket,
            'statuses' => RepairStatus::orderBy('sort_order')->get(),
            'parts' => Part::where('quantity', '>', 0)->orderBy('name')->get(),
            'technicians' => User::whereIn('role', [User::ROLE_TECHNICIAN, User::ROLE_ADMIN])->orderBy('name')->get(),
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load('customer');

        return view('tickets.edit', compact('ticket'));
    }

    public function update(TicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $ticket) {
            // Customers are shared between tickets. Point this ticket at the (name, phone) customer
            // instead of renaming the shared row, so other tickets are untouched and unique(name, phone) is never violated.
            $customer = Customer::firstOrCreate(
                ['name' => $data['customer_name'], 'phone' => $data['customer_phone']],
                ['email' => $data['customer_email'] ?? null],
            );
            if (! $customer->wasRecentlyCreated) {
                // Same customer as before: the typed email replaces the old one (blank clears it).
                // A different existing customer: only fill an email, never erase theirs.
                $email = $data['customer_email'] ?? null;
                if ($customer->id === $ticket->customer_id || $email !== null) {
                    $customer->update(['email' => $email]);
                }
            }

            $ticket->update([
                'customer_id' => $customer->id,
                'device_type' => $data['device_type'],
                'brand' => $data['brand'],
                'model' => $data['model'] ?? null,
                'serial_number' => $data['serial_number'] ?? null,
                'problem_description' => $data['problem_description'],
            ]);
        });

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        // ticket_parts rows are deleted by cascade; trigger trg_restore_part_stock returns the parts to stock
        $number = $ticket->ticket_number;
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', "Ticket {$number} deleted and its parts returned to stock.");
    }
}
