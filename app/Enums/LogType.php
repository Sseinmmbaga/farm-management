<?php

namespace App\Enums;

enum LogType: string
{
    case SEEDING = 'seeding';
    case INPUT = 'input';
    case OBSERVATION = 'observation';
    case HARVEST = 'harvest';
    case ACTIVITY = 'activity';
    case TRAINING = 'training';
    case INSPECTION = 'inspection';

    public function label(): string
    {
        return match($this) {
            self::SEEDING => 'Seeding/Kupanda',
            self::INPUT => 'Input Application',
            self::OBSERVATION => 'Observation',
            self::HARVEST => 'Harvest/Mavuno',
            self::ACTIVITY => 'Activity',
            self::TRAINING => 'Training/Mafunzo',
            self::INSPECTION => 'Inspection/Ukaguzi',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::SEEDING => 'Kupanda',
            self::INPUT => 'Uwekaji Pembejeo',
            self::OBSERVATION => 'Uchunguzi',
            self::HARVEST => 'Mavuno',
            self::ACTIVITY => 'Shughuli',
            self::TRAINING => 'Mafunzo',
            self::INSPECTION => 'Ukaguzi',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::SEEDING => 'bi-flower2',
            self::INPUT => 'bi-droplet',
            self::OBSERVATION => 'bi-eye',
            self::HARVEST => 'bi-basket',
            self::ACTIVITY => 'bi-activity',
            self::TRAINING => 'bi-mortarboard',
            self::INSPECTION => 'bi-clipboard-check',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
