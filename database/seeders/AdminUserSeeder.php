<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table("roles")->where("name", "admin")->value("id");
        
        if ($adminRoleId) {
            $adminExists = User::where("email", "admin@remei.com")->exists();
            
            if (!$adminExists) {
                User::create([
                    "name" => "System Administrator",
                    "email" => "admin@remei.com",
                    "password" => Hash::make("password"),
                    "role_id" => $adminRoleId,
                    "phone" => "+255123456789",
                    "status" => "active",
                ]);
                
                echo "Admin user created: admin@remei.com / password\n";
            } else {
                echo "Admin user already exists.\n";
            }
        }
    }
}