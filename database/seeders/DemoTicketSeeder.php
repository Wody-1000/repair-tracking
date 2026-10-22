<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Part;
use App\Models\RepairStatus;
use App\Models\Ticket;
use App\Models\TicketPart;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

// Sample repairs so the app has something to show. Parts are added through ticket_parts,
// so the PostgreSQL trigger really deducts stock and raises low-stock alerts while seeding.
class DemoTicketSeeder extends Seeder
{
    public function run(): void
    {
        $desk = User::where('email', 'desk@repair.test')->firstOrFail();
        $tech = User::where('email', 'tech@repair.test')->firstOrFail();
        $status = RepairStatus::pluck('id', 'name');

        // [customer, phone, device, brand, model, problem, [status path], [[sku, qty]], labor]
        $samples = [
            ['Juan Dela Cruz', '0917-111-2222', 'Laptop', 'Acer', 'Aspire 5', 'Slow and freezing', ['Received', 'Diagnosing', 'Repairing', 'Ready for Pickup', 'Released'], [['SSD-256-SATA', 1], ['THM-PASTE', 1]], 500],
            ['Maria Santos', '0918-222-3333', 'Laptop', 'Lenovo', 'IdeaPad 3', 'Cracked screen', ['Received', 'Diagnosing', 'Waiting for Parts'], [], 0],
            ['Pedro Reyes', '0919-333-4444', 'Desktop', 'Custom', null, 'No power', ['Received', 'Diagnosing', 'Repairing', 'Ready for Pickup'], [['PSU-500W', 1]], 350],
            ['Ana Garcia', '0920-444-5555', 'Laptop', 'HP', 'Pavilion 14', 'Battery drains fast', ['Received', 'Diagnosing', 'Repairing', 'Released'], [['BAT-LAP-GEN', 2]], 300],
            ['Luis Mendoza', '0921-555-6666', 'Laptop', 'Asus', 'VivoBook 15', 'Keyboard keys not working', ['Received', 'Diagnosing', 'Repairing', 'Released'], [['KB-LAP-GEN', 1]], 250],
            ['Carla Bautista', '0922-666-7777', 'Desktop', 'Dell', 'OptiPlex 3040', 'Needs more memory', ['Received', 'Repairing', 'Released'], [['RAM-DDR4-8-DT', 2]], 200],
            ['Mark Villanueva', '0923-777-8888', 'Laptop', 'Acer', 'Nitro 5', 'Overheating', ['Received', 'Diagnosing'], [], 0],
            ['Joy Ramos', '0924-888-9999', 'Laptop', 'Lenovo', 'ThinkPad E14', 'Screen flickering', ['Received', 'Diagnosing', 'Repairing', 'Released'], [['LCD-156-30P', 1]], 600],
        ];

        foreach ($samples as [$name, $phone, $type, $brand, $model, $problem, $path, $used, $labor]) {
            $customer = Customer::firstOrCreate(['name' => $name, 'phone' => $phone]);
            $final = end($path);

            $ticket = Ticket::create([
                'ticket_number' => Ticket::nextNumber(),
                'customer_id' => $customer->id,
                'device_type' => $type,
                'brand' => $brand,
                'model' => $model,
                'problem_description' => $problem,
                'status_id' => $status[$final],
                'created_by' => $desk->id,
                'assigned_to' => count($path) > 1 ? $tech->id : null,
                'labor_cost' => $labor,
            ]);

            foreach ($path as $i => $step) {
                TicketStatusHistory::create([
                    'ticket_id' => $ticket->id,
                    'status_id' => $status[$step],
                    'changed_by' => $i === 0 ? $desk->id : $tech->id,
                    'note' => $i === 0 ? 'Device received' : null,
                ]);
            }

            foreach ($used as [$sku, $qty]) {
                $part = Part::where('sku', $sku)->firstOrFail();
                TicketPart::create([
                    'ticket_id' => $ticket->id,
                    'part_id' => $part->id,
                    'quantity' => $qty,
                    'unit_price' => $part->unit_price,
                ]);
            }
        }
    }
}
