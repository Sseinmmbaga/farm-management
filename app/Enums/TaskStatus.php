<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::ASSIGNED => 'Assigned',
            self::IN_PROGRESS => 'In Progress',
            self::ON_HOLD => 'On Hold',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::OVERDUE => 'Overdue',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::PENDING => 'Inasubiri',
            self::ASSIGNED => 'Imepewa',
            self::IN_PROGRESS => 'Inaendelea',
            self::ON_HOLD => 'Imesimamishwa',
            self::COMPLETED => 'Imekamilika',
            self::CANCELLED => 'Imefutwa',
            self::OVERDUE => 'Imechelewa',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::ASSIGNED => 'info',
            self::IN_PROGRESS => 'primary',
            self::ON_HOLD => 'secondary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'dark',
            self::OVERDUE => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'bi-clock',
            self::ASSIGNED => 'bi-person-check',
            self::IN_PROGRESS => 'bi-play-circle',
            self::ON_HOLD => 'bi-pause-circle',
            self::COMPLETED => 'bi-check-circle',
            self::CANCELLED => 'bi-x-circle',
            self::OVERDUE => 'bi-exclamation-triangle',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function activeStatuses(): array
    {
        return [
            self::PENDING,
            self::ASSIGNED,
            self::IN_PROGRESS,
            self::ON_HOLD,
        ];
    }
}
