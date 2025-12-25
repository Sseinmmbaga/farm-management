<?php

namespace App\Enums;

enum StockTransactionType: string
{
    case INTAKE = 'intake';
    case ISSUANCE = 'issuance';
    case DISTRIBUTION = 'distribution';
    case RETURN = 'return';
    case ADJUSTMENT = 'adjustment';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match($this) {
            self::INTAKE => 'Stock Intake',
            self::ISSUANCE => 'Stock Issuance',
            self::DISTRIBUTION => 'Distribution to Farmer',
            self::RETURN => 'Stock Return',
            self::ADJUSTMENT => 'Inventory Adjustment',
            self::TRANSFER => 'Stock Transfer',
        };
    }

    public function isIncoming(): bool
    {
        return in_array($this, [self::INTAKE, self::RETURN]);
    }

    public function isOutgoing(): bool
    {
        return in_array($this, [self::ISSUANCE, self::DISTRIBUTION, self::TRANSFER]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
