<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\Settings\DefaultSettings;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (DefaultSettings::all() as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
