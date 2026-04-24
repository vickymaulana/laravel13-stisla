<?php

namespace Tests\Unit;

use App\Support\Settings\DefaultSettings;
use PHPUnit\Framework\TestCase;

class SettingsDefaultsTest extends TestCase
{
    public function test_default_settings_include_current_seeded_keys(): void
    {
        $keys = array_column(DefaultSettings::all(), 'key');

        $this->assertContains('app_name', $keys);
        $this->assertContains('linkedin_url', $keys);
        $this->assertContains('enable_dark_mode', $keys);
    }

    public function test_default_settings_have_unique_keys_and_required_fields(): void
    {
        $settings = DefaultSettings::all();
        $keys = array_column($settings, 'key');

        $this->assertSame($keys, array_unique($keys));

        foreach ($settings as $setting) {
            $this->assertArrayHasKey('key', $setting);
            $this->assertArrayHasKey('value', $setting);
            $this->assertArrayHasKey('type', $setting);
            $this->assertArrayHasKey('group', $setting);
            $this->assertArrayHasKey('label', $setting);
            $this->assertArrayHasKey('description', $setting);
            $this->assertArrayHasKey('order', $setting);
        }
    }
}
