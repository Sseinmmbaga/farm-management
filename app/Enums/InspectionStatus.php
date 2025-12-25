<?php

namespace App\Enums;

enum InspectionStatus: string
{
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case PASSED = 'passed';
    case FAILED = 'failed';
    case PENDING_REVIEW = 'pending_review';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::PASSED => 'Passed',
            self::FAILED => 'Failed',
            self::PENDING_REVIEW => 'Pending Review',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SCHEDULED => 'info',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'secondary',
            self::PASSED => 'success',
            self::FAILED => 'danger',
            self::PENDING_REVIEW => 'warning',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
