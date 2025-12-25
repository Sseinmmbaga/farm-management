<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::LOW => 'Chini',
            self::MEDIUM => 'Wastani',
            self::HIGH => 'Juu',
            self::URGENT => 'Haraka',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'secondary',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LOW => 'bi-arrow-down',
            self::MEDIUM => 'bi-dash',
            self::HIGH => 'bi-arrow-up',
            self::URGENT => 'bi-exclamation-circle',
        };
    }

    public function sortOrder(): int
    {
        return match($this) {
            self::URGENT => 1,
            self::HIGH => 2,
            self::MEDIUM => 3,
            self::LOW => 4,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
