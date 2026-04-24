<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\StoreSettingRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Support\Settings\DefaultSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Application settings management (superadmin only).
 *
 * Settings are grouped by category and support multiple input types
 * including text, number, boolean, email, url, json, and textarea.
 */
class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $settings = Setting::getAllGrouped();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        // Get all boolean settings to handle unchecked checkboxes
        $booleanKeys = Setting::where('type', 'boolean')->pluck('key')->toArray();

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        // Handle unchecked boolean checkboxes (they won't be in the request)
        foreach ($booleanKeys as $key) {
            if (! isset($request->settings[$key])) {
                Setting::where('key', $key)->update(['value' => '0']);
            }
        }

        Setting::clearCache();

        ActivityLog::log('Settings updated', 'System Settings', 'updated');

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Create a new setting.
     */
    public function store(StoreSettingRequest $request): RedirectResponse
    {
        Setting::create($request->only(['key', 'label', 'value', 'type', 'group', 'description', 'order']));

        ActivityLog::log('New setting created: '.$request->key, 'System Settings', 'created');

        return redirect()->route('settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Reset settings to default.
     */
    public function reset(Request $request): RedirectResponse
    {
        if ($request->group) {
            Setting::where('group', $request->group)->delete();
            $message = ucfirst($request->group).' settings reset successfully.';
        } else {
            Setting::truncate();
            $message = 'All settings reset successfully.';
        }

        Setting::clearCache();
        $this->seedDefaultSettings();

        ActivityLog::log('Settings reset', 'System Settings', 'updated');

        return redirect()->route('settings.index')
            ->with('success', $message);
    }

    /**
     * Seed default settings.
     */
    private function seedDefaultSettings(): void
    {
        foreach (DefaultSettings::all() as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
