<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SUPERVISOR = 'supervisor';
    case EXTENSION_OFFICER = 'extension_officer';
    case ICS_INSPECTOR = 'ics_inspector';
    case STOCK_MANAGER = 'stock_manager';
    case ACCOUNTANT = 'accountant';
    case TRAINING_COORDINATOR = 'training_coordinator';
    case PRODUCTION_MANAGER = 'production_manager';
    case FARMER = 'farmer';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::SUPERVISOR => 'Supervisor',
            self::EXTENSION_OFFICER => 'Field Extension Officer',
            self::ICS_INSPECTOR => 'ICS Inspector',
            self::STOCK_MANAGER => 'Stock Manager',
            self::ACCOUNTANT => 'Accountant',
            self::TRAINING_COORDINATOR => 'Training Coordinator',
            self::PRODUCTION_MANAGER => 'Production Manager',
            self::FARMER => 'Farmer',
        };
    }

    public function dashboardRoute(): string
    {
        return match($this) {
            self::ADMIN => 'dashboard.admin',
            self::SUPERVISOR => 'dashboard.supervisor',
            self::EXTENSION_OFFICER => 'dashboard.extension',
            self::ICS_INSPECTOR => 'dashboard.ics',
            self::STOCK_MANAGER => 'dashboard.stock',
            self::ACCOUNTANT => 'dashboard.accountant',
            self::TRAINING_COORDINATOR => 'dashboard.training',
            self::PRODUCTION_MANAGER => 'dashboard.production',
            self::FARMER => 'dashboard.farmer',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
