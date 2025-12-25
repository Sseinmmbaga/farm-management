<?php

namespace App\Services\Farmers;

use App\Models\Farmers\Farmer;
use Illuminate\Support\Facades\DB;

class FarmerService
{
    /**
     * Create a new farmer.
     */
    public function create(array $data): Farmer
    {
        return DB::transaction(function () use ($data) {
            $farmer = Farmer::create($data);

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
