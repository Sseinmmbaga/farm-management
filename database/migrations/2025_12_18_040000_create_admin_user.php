<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $adminRoleId = DB::table("roles")->where("name", "admin")->value("id");
        
        if ($adminRoleId) {
            $adminExists = DB::table("users")->where("email", "admin@remei.com")->exists();
            
            if (!$adminExists) {
                DB::table("users")->insert([
                    "name" => "System Administrator",
                    "email" => "admin@remei.com",
                    "password" => Hash::make("password"),
                    "role_id" => $adminRoleId,
                    "phone" => "+255123456789",
                    "status" => "active",
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table("users")->where("email", "admin@remei.com")->delete();
    }
};