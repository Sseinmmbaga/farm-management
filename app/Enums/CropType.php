<?php

namespace App\Enums;

enum CropType: string
{
    case COTTON = 'cotton';       // Pamba
    case SESAME = 'sesame';       // Ufuta
    case SUNFLOWER = 'sunflower'; // Alizeti
    case MAIZE = 'maize';         // Mahindi
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::COTTON => 'Cotton (Pamba)',
            self::SESAME => 'Sesame (Ufuta)',
            self::SUNFLOWER => 'Sunflower (Alizeti)',
            self::MAIZE => 'Maize (Mahindi)',
            self::OTHER => 'Other',
        };
    }

    public function labelSwahili(): string
    {
        return match($this) {
            self::COTTON => 'Pamba',
            self::SESAME => 'Ufuta',
            self::SUNFLOWER => 'Alizeti',
            self::MAIZE => 'Mahindi',
            self::OTHER => 'Nyingine',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
