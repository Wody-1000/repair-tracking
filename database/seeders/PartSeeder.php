<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\PartCategory;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        // category => [sku, name, quantity, reorder_level, unit_price]
        $catalog = [
            'Storage' => [
                ['SSD-256-SATA', 'SSD 256GB SATA', 12, 5, 1450.00],
                ['SSD-512-NVME', 'SSD 512GB NVMe', 8, 4, 2650.00],
                ['HDD-1TB-25', 'HDD 1TB 2.5 inch', 6, 4, 1800.00],
            ],
            'Memory' => [
                ['RAM-DDR4-8-SO', 'RAM DDR4 8GB SO-DIMM', 15, 5, 1300.00],
                ['RAM-DDR4-8-DT', 'RAM DDR4 8GB Desktop', 10, 5, 1250.00],
            ],
            'Power' => [
                ['PSU-500W', 'Power Supply 500W', 5, 3, 1750.00],
                ['LAP-CHG-65W', 'Laptop Charger 65W Universal', 9, 4, 850.00],
                ['BAT-LAP-GEN', 'Laptop Battery Generic', 4, 3, 1600.00],
            ],
            'Display & Input' => [
                ['LCD-156-30P', 'Laptop LCD 15.6 inch 30-pin', 3, 3, 3200.00],
                ['KB-LAP-GEN', 'Laptop Keyboard Generic', 7, 4, 650.00],
            ],
            'Accessories' => [
                ['THM-PASTE', 'Thermal Paste Tube', 20, 8, 180.00],
                ['SATA-CABLE', 'SATA Data Cable', 25, 10, 60.00],
            ],
        ];

        foreach ($catalog as $categoryName => $parts) {
            $category = PartCategory::create(['name' => $categoryName]);

            foreach ($parts as [$sku, $name, $qty, $reorder, $price]) {
                Part::create([
                    'sku' => $sku, 'name' => $name, 'category_id' => $category->id,
                    'quantity' => $qty, 'reorder_level' => $reorder, 'unit_price' => $price,
                ]);
            }
        }
    }
}
