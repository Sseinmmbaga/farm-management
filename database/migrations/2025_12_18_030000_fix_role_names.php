<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clear the roles table
        DB::table("roles")->delete();
        
        // Insert new roles with correct names
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

        foreach ($roles as $role) {
            DB::table("roles")->insert([
                "name" => $role["name"],
                "description" => $role["description"],
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
        
        // Update all users to have the farmer role (ID should be 9 for farmer)
        $farmerRoleId = DB::table("roles")->where("name", "farmer")->value("id");
        if ($farmerRoleId) {
            DB::table("users")->update(["role_id" => $farmerRoleId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We cannot easily reverse this
    }
};