<?php

namespace App\Services\Logs;

use App\Enums\LogType;
use App\Models\Logs\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogService
{
    /**
     * Create a new activity log with type-specific data.
     */
    public function create(array $validated, array $allData): ActivityLog
    {
        return DB::transaction(function () use ($validated, $allData) {
            $validated['created_by'] = Auth::id();
            $validated['status'] = $validated['status'] ?? 'pending';

            $log = ActivityLog::create($validated);

            // Handle type-specific data
            $this->handleTypeSpecificData($log, $allData);

            // Handle image uploads
            $this->handleImages($log, $allData);

            return $log;
        });
    }

    /**
     * Update an existing activity log.
     */
    public function update(ActivityLog $log, array $validated, array $allData): ActivityLog
    {
        return DB::transaction(function () use ($log, $validated, $allData) {
            $log->update($validated);

            // Handle type-specific data updates
            $this->handleTypeSpecificData($log, $allData);

            // Handle image uploads
            $this->handleImages($log, $allData);

            return $log->fresh();
        });
    }

    /**
     * Handle type-specific data based on log type.
     */
    protected function handleTypeSpecificData(ActivityLog $log, array $data): void
    {
        $type = LogType::tryFrom($log->type);

        if (!$type) {
            return;
        }

        match ($type) {
            LogType::SEEDING => $this->handleSeedingData($log, $data),
            LogType::INPUT => $this->handleInputData($log, $data),
            LogType::OBSERVATION => $this->handleObservationData($log, $data),
            LogType::HARVEST => $this->handleHarvestData($log, $data),
            default => null,
        };
    }

    protected function handleSeedingData(ActivityLog $log, array $data): void
    {
        if ($log->seedingLog) {
            $log->seedingLog->update($data['seeding'] ?? []);
        } elseif (!empty($data['seeding'])) {
            $log->seedingLog()->create($data['seeding']);
        }
    }

    protected function handleInputData(ActivityLog $log, array $data): void
    {
        if ($log->inputLog) {
            $log->inputLog->update($data['input'] ?? []);
        } elseif (!empty($data['input'])) {
            $log->inputLog()->create($data['input']);
        }
    }

    protected function handleObservationData(ActivityLog $log, array $data): void
    {
        if ($log->observationLog) {
            $log->observationLog->update($data['observation'] ?? []);
        } elseif (!empty($data['observation'])) {
            $log->observationLog()->create($data['observation']);
        }
    }

    protected function handleHarvestData(ActivityLog $log, array $data): void
    {
        if ($log->harvestLog) {
            $log->harvestLog->update($data['harvest'] ?? []);
        } elseif (!empty($data['harvest'])) {
            $log->harvestLog()->create($data['harvest']);
        }
    }

    protected function handleImages(ActivityLog $log, array $data): void
    {
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                if ($image->isValid()) {
                    $path = $image->store('logs/' . $log->id, 'public');
                    $log->images()->create(['path' => $path]);
                }
            }
        }
    }
}
