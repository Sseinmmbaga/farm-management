<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default users for each role (matching your existing auth system)
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::ADMIN,
                'phone' => '+255700000001',
            ],
            [
                'name' => 'Supervisor User',
                'email' => 'supervisor@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::SUPERVISOR,
                'phone' => '+255700000002',
            ],
            [
                'name' => 'Extension Officer',
                'email' => 'extension@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::EXTENSION_OFFICER,
                'phone' => '+255700000003',
            ],
            [
                'name' => 'ICS Inspector',
                'email' => 'ics@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::ICS_INSPECTOR,
                'phone' => '+255700000004',
            ],
            [
                'name' => 'Stock Manager',
                'email' => 'stock@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::STOCK_MANAGER,
                'phone' => '+255700000005',
            ],
            [
                'name' => 'Accountant',
                'email' => 'accountant@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::ACCOUNTANT,
                'phone' => '+255700000006',
            ],
            [
                'name' => 'Training Coordinator',
                'email' => 'training@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::TRAINING_COORDINATOR,
                'phone' => '+255700000007',
            ],
            [
                'name' => 'Production Manager',
                'email' => 'production@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::PRODUCTION_MANAGER,
                'phone' => '+255700000008',
            ],
            [
                'name' => 'Sample Farmer',
                'email' => 'farmer@remei.com',
                'password' => Hash::make('password123'),
                'role' => UserRole::FARMER,
                'phone' => '+255700000009',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Created ' . count($users) . ' default users.');
    }
}
