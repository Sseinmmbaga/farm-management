<?php

namespace App\Services\Assets;

use App\Enums\AssetType;
use App\Models\Assets\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetService
{
    /**
     * Create a new asset with type-specific data.
     */
    public function create(array $validated, array $allData): Asset
    {
        return DB::transaction(function () use ($validated, $allData) {
            $validated['created_by'] = Auth::id();
            $validated['status'] = $validated['status'] ?? 'active';

            $asset = Asset::create($validated);

            // Handle type-specific data
            $this->handleTypeSpecificData($asset, $allData);

            return $asset;
        });
    }

    /**
     * Update an existing asset.
     */
    public function update(Asset $asset, array $validated, array $allData): Asset
    {
        return DB::transaction(function () use ($asset, $validated, $allData) {
            $asset->update($validated);

            // Handle type-specific data updates
            $this->handleTypeSpecificData($asset, $allData);

            return $asset->fresh();
        });
    }

    /**
     * Handle type-specific data based on asset type.
     */
    protected function handleTypeSpecificData(Asset $asset, array $data): void
    {
        $type = AssetType::tryFrom($asset->type);

        if (!$type) {
            return;
        }

        match ($type) {
            AssetType::LAND => $this->handleLandData($asset, $data),
            AssetType::CROP => $this->handleCropData($asset, $data),
            AssetType::EQUIPMENT => $this->handleEquipmentData($asset, $data),
            AssetType::MATERIAL => $this->handleMaterialData($asset, $data),
            AssetType::GROUP => $this->handleGroupData($asset, $data),
            default => null,
        };
    }

    protected function handleLandData(Asset $asset, array $data): void
    {
        if ($asset->landAsset) {
            $asset->landAsset->update($data['land'] ?? []);
        } elseif (!empty($data['land'])) {
            $asset->landAsset()->create($data['land']);
        }
    }

    protected function handleCropData(Asset $asset, array $data): void
    {
        if ($asset->cropAsset) {
            $asset->cropAsset->update($data['crop'] ?? []);
        } elseif (!empty($data['crop'])) {
            $asset->cropAsset()->create($data['crop']);
        }
    }

    protected function handleEquipmentData(Asset $asset, array $data): void
    {
        if ($asset->equipmentAsset) {
            $asset->equipmentAsset->update($data['equipment'] ?? []);
        } elseif (!empty($data['equipment'])) {
            $asset->equipmentAsset()->create($data['equipment']);
        }
    }

    protected function handleMaterialData(Asset $asset, array $data): void
    {
        if ($asset->materialAsset) {
            $asset->materialAsset->update($data['material'] ?? []);
        } elseif (!empty($data['material'])) {
            $asset->materialAsset()->create($data['material']);
        }
    }

    protected function handleGroupData(Asset $asset, array $data): void
    {
        if ($asset->groupAsset) {
            $asset->groupAsset->update($data['group'] ?? []);
        } elseif (!empty($data['group'])) {
            $asset->groupAsset()->create($data['group']);
        }
    }
}
