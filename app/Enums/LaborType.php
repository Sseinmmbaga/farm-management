<?php

namespace App\Enums;

enum LaborType: string
{
    case PERMANENT = 'permanent';
    case SEASONAL = 'seasonal';
    case CASUAL = 'casual';
    case CONTRACT = 'contract';
    case FAMILY = 'family';
    case VOLUNTEER = 'volunteer';

    public function label(): string
    {
        return match($this) {
            self::PERMANENT => 'Permanent Worker',
            self::SEASONAL => 'Seasonal Worker',
            self::CASUAL => 'Casual Labor',
            self::CONTRACT => 'Contract Worker',
            self::FAMILY => 'Family Member',
            self::VOLUNTEER => 'Volunteer',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::PERMANENT => 'Mfanyakazi wa Kudumu',
            self::SEASONAL => 'Mfanyakazi wa Msimu',
            self::CASUAL => 'Kibarua',
            self::CONTRACT => 'Mfanyakazi wa Mkataba',
            self::FAMILY => 'Mwanafamilia',
            self::VOLUNTEER => 'Kujitolea',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PERMANENT => 'success',
            self::SEASONAL => 'info',
            self::CASUAL => 'warning',
            self::CONTRACT => 'primary',
            self::FAMILY => 'secondary',
            self::VOLUNTEER => 'purple',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PERMANENT => 'bi-person-badge',
            self::SEASONAL => 'bi-calendar-event',
            self::CASUAL => 'bi-person',
            self::CONTRACT => 'bi-file-earmark-text',
            self::FAMILY => 'bi-people',
            self::VOLUNTEER => 'bi-heart',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function paidTypes(): array
    {
        return [
            self::PERMANENT,
            self::SEASONAL,
            self::CASUAL,
            self::CONTRACT,
        ];
    }
}
