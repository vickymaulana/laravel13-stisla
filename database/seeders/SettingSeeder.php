<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Laravel Admin',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of your application',
                'order' => 1
            ],
            [
                'key' => 'app_description',
                'value' => 'A modern admin panel built with Laravel 13 and Stisla',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Application Description',
                'description' => 'Short description of your application',
                'order' => 2
            ],
            [
                'key' => 'app_logo',
                'value' => '/img/logo.png',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Application Logo',
                'description' => 'Path to your application logo',
                'order' => 3
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'label' => 'Maintenance Mode',
                'description' => 'Enable maintenance mode',
                'order' => 4
            ],
            
            // Email Settings
            [
                'key' => 'contact_email',
                'value' => 'admin@example.com',
                'type' => 'email',
                'group' => 'email',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'order' => 1
            ],
            [
                'key' => 'support_email',
                'value' => 'support@example.com',
                'type' => 'email',
                'group' => 'email',
                'label' => 'Support Email',
                'description' => 'Support email address',
                'order' => 2
            ],
            
            // Social Media
            [
                'key' => 'facebook_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Your Facebook page URL',
                'order' => 1
            ],
            [
                'key' => 'twitter_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'Twitter URL',
                'description' => 'Your Twitter profile URL',
                'order' => 2
            ],
            [
                'key' => 'instagram_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Your Instagram profile URL',
                'order' => 3
            ],
            [
                'key' => 'linkedin_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'LinkedIn URL',
                'description' => 'Your LinkedIn profile URL',
                'order' => 4
            ],
            
            // Appearance
            [
                'key' => 'items_per_page',
                'value' => '20',
                'type' => 'number',
                'group' => 'appearance',
                'label' => 'Items Per Page',
                'description' => 'Number of items to display per page',
                'order' => 1
            ],
            [
                'key' => 'theme_color',
                'value' => '#6777ef',
                'type' => 'text',
                'group' => 'appearance',
                'label' => 'Primary Color',
                'description' => 'Primary theme color (hex code)',
                'order' => 2
            ],
            [
                'key' => 'enable_dark_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Enable Dark Mode',
                'description' => 'Allow users to switch to dark mode',
                'order' => 3
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
