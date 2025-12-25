<?php

namespace App\Enums;

enum TaskType: string
{
    case FIELD_PREPARATION = 'field_preparation';
    case SEEDING = 'seeding';
    case WEEDING = 'weeding';
    case IRRIGATION = 'irrigation';
    case FERTILIZATION = 'fertilization';
    case PEST_CONTROL = 'pest_control';
    case HARVESTING = 'harvesting';
    case POST_HARVEST = 'post_harvest';
    case INSPECTION = 'inspection';
    case TRAINING = 'training';
    case MAINTENANCE = 'maintenance';
    case TRANSPORT = 'transport';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::FIELD_PREPARATION => 'Field Preparation',
            self::SEEDING => 'Seeding/Planting',
            self::WEEDING => 'Weeding',
            self::IRRIGATION => 'Irrigation',
            self::FERTILIZATION => 'Fertilization',
            self::PEST_CONTROL => 'Pest Control',
            self::HARVESTING => 'Harvesting',
            self::POST_HARVEST => 'Post-Harvest Processing',
            self::INSPECTION => 'Inspection',
            self::TRAINING => 'Training',
            self::MAINTENANCE => 'Equipment Maintenance',
            self::TRANSPORT => 'Transport/Logistics',
            self::OTHER => 'Other',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::FIELD_PREPARATION => 'Kuandaa Shamba',
            self::SEEDING => 'Kupanda',
            self::WEEDING => 'Kupalilia',
            self::IRRIGATION => 'Kumwagilia',
            self::FERTILIZATION => 'Kuweka Mbolea',
            self::PEST_CONTROL => 'Kudhibiti Wadudu',
            self::HARVESTING => 'Kuvuna',
            self::POST_HARVEST => 'Usindikaji Baada ya Mavuno',
            self::INSPECTION => 'Ukaguzi',
            self::TRAINING => 'Mafunzo',
            self::MAINTENANCE => 'Ukarabati wa Vifaa',
            self::TRANSPORT => 'Usafiri',
            self::OTHER => 'Nyingine',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::FIELD_PREPARATION => 'bi-grid-3x3',
            self::SEEDING => 'bi-flower2',
            self::WEEDING => 'bi-scissors',
            self::IRRIGATION => 'bi-droplet',
            self::FERTILIZATION => 'bi-moisture',
            self::PEST_CONTROL => 'bi-bug',
            self::HARVESTING => 'bi-basket',
            self::POST_HARVEST => 'bi-box-seam',
            self::INSPECTION => 'bi-clipboard-check',
            self::TRAINING => 'bi-mortarboard',
            self::MAINTENANCE => 'bi-tools',
            self::TRANSPORT => 'bi-truck',
            self::OTHER => 'bi-three-dots',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::FIELD_PREPARATION => 'brown',
            self::SEEDING => 'success',
            self::WEEDING => 'warning',
            self::IRRIGATION => 'info',
            self::FERTILIZATION => 'secondary',
            self::PEST_CONTROL => 'danger',
            self::HARVESTING => 'primary',
            self::POST_HARVEST => 'dark',
            self::INSPECTION => 'indigo',
            self::TRAINING => 'purple',
            self::MAINTENANCE => 'orange',
            self::TRANSPORT => 'teal',
            self::OTHER => 'secondary',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fieldTasks(): array
    {
        return [
            self::FIELD_PREPARATION,
            self::SEEDING,
            self::WEEDING,
            self::IRRIGATION,
            self::FERTILIZATION,
            self::PEST_CONTROL,
            self::HARVESTING,
            self::POST_HARVEST,
        ];
    }
}
