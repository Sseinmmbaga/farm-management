<?php

namespace App\Enums;

enum AssetType: string
{
    case LAND = 'land';
    case CROP = 'crop';
    case EQUIPMENT = 'equipment';
    case MATERIAL = 'material';
    case GROUP = 'group';

    public function label(): string
    {
        return match($this) {
            self::LAND => 'Land/Shamba',
            self::CROP => 'Crop/Mazao',
            self::EQUIPMENT => 'Equipment/Vifaa',
            self::MATERIAL => 'Material/Pembejeo',
            self::GROUP => 'Group/Kikundi',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::LAND => 'Shamba',
            self::CROP => 'Mazao',
            self::EQUIPMENT => 'Vifaa',
            self::MATERIAL => 'Pembejeo',
            self::GROUP => 'Kikundi',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LAND => 'bi-geo-alt',
            self::CROP => 'bi-flower1',
            self::EQUIPMENT => 'bi-tools',
            self::MATERIAL => 'bi-box-seam',
            self::GROUP => 'bi-people',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
