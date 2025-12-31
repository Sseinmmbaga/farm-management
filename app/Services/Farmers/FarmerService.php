<?php

namespace App\Services\Farmers;

use App\Enums\UserRole;
use App\Models\Farmers\Farmer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FarmerService
{
    /**
     * Create a new farmer with associated user account.
     */
    public function create(array $data): Farmer
    {
        return DB::transaction(function () use ($data) {
            // Extract user-related data
            $password = $data['password'] ?? null;
            $generatePassword = empty($password);

            // Generate password if not provided
            if ($generatePassword) {
                $password = Str::random(8);
            }

            // Create the user account for the farmer
            $user = User::create([
                'name' => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($password),
                'role' => UserRole::FARMER,
                'is_active' => true,
            ]);

            // Remove password from farmer data (not needed in farmers table)
            unset($data['password']);

            // Add user_id to farmer data
            $data['user_id'] = $user->id;

            // Create the farmer profile
            $farmer = Farmer::create($data);

            // Store the plain password temporarily for display (not persisted)
            $farmer->generatedPassword = $generatePassword ? $password : null;

            return $farmer;
        });
    }

    /**
     * Update an existing farmer.
     */
    public function update(Farmer $farmer, array $data): Farmer
    {
        return DB::transaction(function () use ($farmer, $data) {
            $farmer->update($data);

            return $farmer->fresh();
        });
    }
}
