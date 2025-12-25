<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $defaultSettings = [
            ['key' => 'general_organization_name', 'value' => 'Remei Farm OS', 'type' => 'string', 'description' => 'Organization name'],
            ['key' => 'general_organization_email', 'value' => '', 'type' => 'string', 'description' => 'Organization email'],
            ['key' => 'general_organization_phone', 'value' => '', 'type' => 'string', 'description' => 'Organization phone'],
            ['key' => 'general_organization_address', 'value' => '', 'type' => 'string', 'description' => 'Organization address'],
            ['key' => 'general_default_currency', 'value' => 'TZS', 'type' => 'string', 'description' => 'Default currency'],
            ['key' => 'general_date_format', 'value' => 'd/m/Y', 'type' => 'string', 'description' => 'Date format'],
            ['key' => 'general_timezone', 'value' => 'Africa/Dar_es_Salaam', 'type' => 'string', 'description' => 'Timezone'],
            ['key' => 'farm_default_unit', 'value' => 'hectares', 'type' => 'string', 'description' => 'Default farm size unit'],
            ['key' => 'farm_min_size', 'value' => '0.1', 'type' => 'decimal', 'description' => 'Minimum farm size'],
            ['key' => 'farm_max_size', 'value' => '100', 'type' => 'decimal', 'description' => 'Maximum farm size'],
            ['key' => 'farm_require_gps', 'value' => '1', 'type' => 'boolean', 'description' => 'Require GPS coordinates'],
            ['key' => 'farm_require_boundaries', 'value' => '0', 'type' => 'boolean', 'description' => 'Require farm boundaries'],
            ['key' => 'farm_season_duration', 'value' => '6', 'type' => 'integer', 'description' => 'Default season duration in months'],
            ['key' => 'certification_conversion_period', 'value' => '36', 'type' => 'integer', 'description' => 'Conversion period in months'],
            ['key' => 'certification_inspection_frequency', 'value' => '12', 'type' => 'integer', 'description' => 'Inspection frequency in months'],
            ['key' => 'certification_require_annual', 'value' => '1', 'type' => 'boolean', 'description' => 'Require annual inspection'],
            ['key' => 'certification_auto_expire', 'value' => '1', 'type' => 'boolean', 'description' => 'Auto expire certification'],
            ['key' => 'certification_validity', 'value' => '12', 'type' => 'integer', 'description' => 'Certification validity in months'],
            ['key' => 'notification_email_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable email notifications'],
            ['key' => 'notification_sms_enabled', 'value' => '0', 'type' => 'boolean', 'description' => 'Enable SMS notifications'],
            ['key' => 'notification_inspection_reminder', 'value' => '7', 'type' => 'integer', 'description' => 'Inspection reminder days'],
            ['key' => 'notification_certification_reminder', 'value' => '30', 'type' => 'integer', 'description' => 'Certification expiry reminder days'],
            ['key' => 'notification_task_reminder', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable task reminders'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
