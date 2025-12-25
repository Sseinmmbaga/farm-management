<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();

        // Group settings by category
        $generalSettings = $this->getSettingsByPrefix($settings, 'general_');
        $farmSettings = $this->getSettingsByPrefix($settings, 'farm_');
        $certificationSettings = $this->getSettingsByPrefix($settings, 'certification_');
        $notificationSettings = $this->getSettingsByPrefix($settings, 'notification_');

        return view('settings.index', compact(
            'settings',
            'generalSettings',
            'farmSettings',
            'certificationSettings',
            'notificationSettings'
        ));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'nullable|email|max:255',
            'organization_phone' => 'nullable|string|max:50',
            'organization_address' => 'nullable|string|max:500',
            'default_currency' => 'required|string|max:10',
            'date_format' => 'required|string|max:20',
            'timezone' => 'required|string|max:50',
        ]);

        $this->saveSetting('general_organization_name', $request->organization_name);
        $this->saveSetting('general_organization_email', $request->organization_email);
        $this->saveSetting('general_organization_phone', $request->organization_phone);
        $this->saveSetting('general_organization_address', $request->organization_address);
        $this->saveSetting('general_default_currency', $request->default_currency);
        $this->saveSetting('general_date_format', $request->date_format);
        $this->saveSetting('general_timezone', $request->timezone);

        Cache::forget('system_settings');

        return back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Update farm settings.
     */
    public function updateFarm(Request $request)
    {
        $request->validate([
            'default_farm_unit' => 'required|string|in:hectares,acres',
            'min_farm_size' => 'required|numeric|min:0',
            'max_farm_size' => 'required|numeric|min:0',
            'require_gps_coordinates' => 'boolean',
            'require_farm_boundaries' => 'boolean',
            'default_season_duration' => 'required|integer|min:1|max:24',
        ]);

        $this->saveSetting('farm_default_unit', $request->default_farm_unit);
        $this->saveSetting('farm_min_size', $request->min_farm_size);
        $this->saveSetting('farm_max_size', $request->max_farm_size);
        $this->saveSetting('farm_require_gps', $request->boolean('require_gps_coordinates') ? '1' : '0');
        $this->saveSetting('farm_require_boundaries', $request->boolean('require_farm_boundaries') ? '1' : '0');
        $this->saveSetting('farm_season_duration', $request->default_season_duration);

        Cache::forget('system_settings');

        return back()->with('success', 'Farm settings updated successfully.');
    }

    /**
     * Update certification settings.
     */
    public function updateCertification(Request $request)
    {
        $request->validate([
            'conversion_period_months' => 'required|integer|min:12|max:60',
            'inspection_frequency_months' => 'required|integer|min:1|max:24',
            'require_annual_inspection' => 'boolean',
            'auto_expire_certification' => 'boolean',
            'certification_validity_months' => 'required|integer|min:6|max:60',
        ]);

        $this->saveSetting('certification_conversion_period', $request->conversion_period_months);
        $this->saveSetting('certification_inspection_frequency', $request->inspection_frequency_months);
        $this->saveSetting('certification_require_annual', $request->boolean('require_annual_inspection') ? '1' : '0');
        $this->saveSetting('certification_auto_expire', $request->boolean('auto_expire_certification') ? '1' : '0');
        $this->saveSetting('certification_validity', $request->certification_validity_months);

        Cache::forget('system_settings');

        return back()->with('success', 'Certification settings updated successfully.');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'inspection_reminder_days' => 'required|integer|min:1|max:30',
            'certification_expiry_reminder_days' => 'required|integer|min:1|max:90',
            'task_reminder_enabled' => 'boolean',
        ]);

        $this->saveSetting('notification_email_enabled', $request->boolean('email_notifications') ? '1' : '0');
        $this->saveSetting('notification_sms_enabled', $request->boolean('sms_notifications') ? '1' : '0');
        $this->saveSetting('notification_inspection_reminder', $request->inspection_reminder_days);
        $this->saveSetting('notification_certification_reminder', $request->certification_expiry_reminder_days);
        $this->saveSetting('notification_task_reminder', $request->boolean('task_reminder_enabled') ? '1' : '0');

        Cache::forget('system_settings');

        return back()->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Save a single setting.
     */
    private function saveSetting(string $key, $value): void
    {
        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get settings by prefix.
     */
    private function getSettingsByPrefix(array $settings, string $prefix): array
    {
        return collect($settings)
            ->filter(fn($value, $key) => str_starts_with($key, $prefix))
            ->mapWithKeys(fn($value, $key) => [str_replace($prefix, '', $key) => $value])
            ->toArray();
    }
}
