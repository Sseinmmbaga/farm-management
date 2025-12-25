<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CropTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a reference table for crop types if needed
     */
    public function run(): void
    {
        // This is for reference - crop types are defined in config/remei.php
        // But we can create a seasons table entry for current season

        DB::table('seasons')->insertOrIgnore([
            [
                'name' => '2024/2025',
                'start_date' => '2024-10-01',
                'end_date' => '2025-06-30',
                'is_current' => true,
                'is_active' => true,
                'notes' => 'Current farming season',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2023/2024',
                'start_date' => '2023-10-01',
                'end_date' => '2024-06-30',
                'is_current' => false,
                'is_active' => true,
                'notes' => 'Previous farming season',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Seeded seasons data.');
    }
}
