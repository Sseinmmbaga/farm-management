<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ["name" => "admin", "description" => "System administrator with full access"],
            ["name" => "accountant", "description" => "Handles financial transactions and reports"],
            ["name" => "production_manager", "description" => "Manages production plans and records"],
            ["name" => "ics_inspector", "description" => "Internal Control System inspector"],
            ["name" => "supervisor", "description" => "Supervises field extension officers"],
            ["name" => "extension_officer", "description" => "Field extension officer"],
            ["name" => "stock_manager", "description" => "Manages stock and inventory"],
            ["name" => "training_coordinator", "description" => "Training coordinator"],
            ["name" => "farmer", "description" => "Registered farmer"],
        ];

        // Clear existing roles
        DB::table("roles")->delete();

        foreach ($roles as $role) {
            DB::table("roles")->insert([
                "name" => $role["name"],
                "description" => $role["description"],
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}