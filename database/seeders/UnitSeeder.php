<?php

namespace Database\Seeders;

use App\Models\Quantities\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitsConfig = config('units.units');
        $sortOrder = 0;

        foreach ($unitsConfig as $category => $units) {
            foreach ($units as $unitData) {
                Unit::updateOrCreate(
                    [
                        'symbol' => $unitData['symbol'],
                        'category' => $category,
                    ],
                    [
                        'name' => $unitData['name'],
                        'name_sw' => $unitData['name_sw'] ?? null,
                        'conversion_factor' => $unitData['conversion_factor'],
                        'base_unit' => $unitData['base_unit'] ?? null,
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ]
                );
            }
        }

        $this->command->info('Seeded units of measurement.');
    }
}
