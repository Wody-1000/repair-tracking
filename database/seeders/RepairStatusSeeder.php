<?php

namespace Database\Seeders;

use App\Models\RepairStatus;
use Illuminate\Database\Seeder;

class RepairStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Received', 'Diagnosing', 'Waiting for Parts', 'Repairing', 'Ready for Pickup', 'Released'];

        foreach ($statuses as $i => $name) {
            RepairStatus::create(['name' => $name, 'sort_order' => $i + 1]);
        }
    }
}
