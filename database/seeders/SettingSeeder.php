<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Setting Groups ───────────────────────────────────────────────────
        $groups = [
            ['key' => 'general',    'name' => 'General',           'icon' => 'Settings',       'sort_order' => 1,  'description' => 'Basic site information, identity, SEO, and appearance.'],
            ['key' => 'media',      'name' => 'Media',             'icon' => 'Image',           'sort_order' => 2,  'description' => 'File upload and media library settings.'],
            ['key' => 'mail',       'name' => 'Mail',              'icon' => 'Mail',            'sort_order' => 3,  'description' => 'Email sending configuration.'],
            ['key' => 'security',   'name' => 'Security',          'icon' => 'Shield',          'sort_order' => 4,  'description' => 'Authentication, rate limiting, and maintenance.'],
        ];

        foreach ($groups as $group) {
            SettingGroup::updateOrCreate(['key' => $group['key']], $group);
        }

        // ─── Settings ─────────────────────────────────────────────────────────
        $settings = [

            // General
            ['group' => 'general', 'key' => 'general.site_name',         'value' => 'My Blog',         'type' => 'string',  'description' => 'The name of your site.'],
            ['group' => 'general', 'key' => 'general.tagline',           'value' => 'A modern blog',   'type' => 'string',  'description' => 'A short description shown below the site name.'],
            ['group' => 'general', 'key' => 'general.site_description',  'value' => 'Welcome to my modern blog where we share the latest updates.', 'type' => 'text', 'description' => 'Detailed description of the site for general display and fallback SEO.'],
            ['group' => 'general', 'key' => 'general.logo_url',      'value' => null,              'type' => 'string',  'description' => 'URL of your site logo image.'],
            ['group' => 'general', 'key' => 'general.favicon_url',   'value' => null,              'type' => 'string',  'description' => 'URL of your favicon (.ico or .png).'],
            ['group' => 'general', 'key' => 'general.timezone',      'value' => 'Asia/Jakarta',    'type' => 'string',  'description' => 'Default timezone for date display.'],
            ['group' => 'general', 'key' => 'general.language',      'value' => 'id',              'type' => 'string',  'description' => 'Default site language code (e.g. id, en).'],
            ['group' => 'general', 'key' => 'general.date_format',   'value' => 'd M Y',           'type' => 'string',  'description' => 'PHP date format for displaying dates.'],

            // SEO (now part of general)
            ['group' => 'general', 'key' => 'seo.custom_seo_enabled',   'value' => false,             'type' => 'boolean', 'description' => 'Enable custom SEO configurations. If disabled, SEO is generated automatically from Branding.'],
            ['group' => 'general', 'key' => 'seo.meta_title_suffix',    'value' => '| My Blog',       'type' => 'string',  'description' => 'Appended to every page title tag.'],
            ['group' => 'general', 'key' => 'seo.meta_description',     'value' => 'A modern blog',   'type' => 'string',  'description' => 'Default meta description for pages without one.'],
            ['group' => 'general', 'key' => 'seo.og_image_url',         'value' => null,              'type' => 'string',  'description' => 'Default Open Graph image for social sharing.'],
            ['group' => 'general', 'key' => 'seo.google_analytics_id',  'value' => null,              'type' => 'string',  'description' => 'Google Analytics measurement ID (GA-XXXXXX).'],
            ['group' => 'general', 'key' => 'seo.robots',               'value' => 'index,follow',    'type' => 'string',  'description' => 'Default robots meta tag value.'],
            ['group' => 'general', 'key' => 'seo.sitemap_enabled',      'value' => true,              'type' => 'boolean', 'description' => 'Enable automatic XML sitemap generation.'],

            // Media
            ['group' => 'media', 'key' => 'media.max_upload_size_mb', 'value' => 10,                                       'type' => 'integer', 'description' => 'Maximum file upload size in megabytes.'],
            ['group' => 'media', 'key' => 'media.allowed_types',      'value' => ['jpg','jpeg','png','gif','webp','pdf','mp4'], 'type' => 'json',    'description' => 'Allowed file extensions for upload.'],
            ['group' => 'media', 'key' => 'media.image_quality',      'value' => 85,                                       'type' => 'integer', 'description' => 'JPEG/WebP compression quality (1-100).'],
            ['group' => 'media', 'key' => 'media.disk',               'value' => 'public',                                 'type' => 'string',  'description' => 'Storage disk for uploads (public or s3).'],
            ['group' => 'media', 'key' => 'media.auto_optimize',      'value' => true,                                     'type' => 'boolean', 'description' => 'Auto-optimize images on upload.'],

            // Mail
            ['group' => 'mail', 'key' => 'mail.use_custom_smtp', 'value' => false,             'type' => 'boolean', 'is_env' => false, 'description' => 'Use custom SMTP instead of .env default.'],
            ['group' => 'mail', 'key' => 'mail.from_name',    'value' => 'My Blog',          'type' => 'string', 'is_env' => false, 'description' => 'Sender name shown in emails.'],
            ['group' => 'mail', 'key' => 'mail.from_email',   'value' => 'hello@domain.com', 'type' => 'string', 'is_env' => false, 'description' => 'Sender email address.'],
            ['group' => 'mail', 'key' => 'mail.driver',       'value' => 'smtp',             'type' => 'string', 'is_env' => false, 'description' => 'Mail transport driver.'],
            ['group' => 'mail', 'key' => 'mail.host',         'value' => '127.0.0.1',        'type' => 'string',  'is_env' => false, 'description' => 'SMTP host.'],
            ['group' => 'mail', 'key' => 'mail.port',         'value' => 1025,               'type' => 'integer', 'is_env' => false, 'description' => 'SMTP port.'],
            ['group' => 'mail', 'key' => 'mail.username',     'value' => null,               'type' => 'string',  'is_env' => false, 'description' => 'SMTP username.'],
            ['group' => 'mail', 'key' => 'mail.password',     'value' => null,               'type' => 'string',  'is_env' => true, 'is_locked' => false, 'description' => 'SMTP password.'],
            ['group' => 'mail', 'key' => 'mail.encryption',   'value' => 'tls',              'type' => 'string',  'is_env' => false, 'description' => 'SMTP encryption (tls/ssl).'],

            // Security
            ['group' => 'security', 'key' => 'security.registration_enabled',       'value' => true,  'type' => 'boolean', 'description' => 'Allow new user registrations.'],
            ['group' => 'security', 'key' => 'security.login_attempts',              'value' => 5,     'type' => 'integer', 'description' => 'Max failed login attempts before throttle.'],
            ['group' => 'security', 'key' => 'security.session_lifetime',            'value' => 120,   'type' => 'integer', 'description' => 'Session lifetime in minutes.'],
            ['group' => 'security', 'key' => 'security.captcha_enabled',             'value' => false, 'type' => 'boolean', 'description' => 'Enable CAPTCHA on login and registration.'],
            ['group' => 'security', 'key' => 'security.maintenance_mode',            'value' => false, 'type' => 'boolean', 'description' => 'Put the site in maintenance mode.'],
            ['group' => 'security', 'key' => 'security.maintenance_whitelist_ips',   'value' => [],    'type' => 'json',    'description' => 'IPs that bypass maintenance mode.'],
            ['group' => 'security', 'key' => 'security.force_https',                 'value' => false, 'type' => 'boolean', 'description' => 'Force HTTPS redirect for all requests.'],

            // Appearance / Theme
            ['group' => 'general', 'key' => 'appearance.primary_color',      'value' => '#ff9100ff',       'type' => 'string', 'description' => 'Primary brand color (hex).'],
            ['group' => 'general', 'key' => 'appearance.secondary_color',    'value' => '#f2b12eff',       'type' => 'string', 'description' => 'Secondary brand color (hex).'],


        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(
                ['key' => $data['key']],
                array_merge([
                    'group'       => $data['group'],
                    'type'        => $data['type'] ?? 'string',
                    'is_env'      => $data['is_env'] ?? false,
                    'is_locked'   => $data['is_locked'] ?? false,
                    'description' => $data['description'] ?? null,
                ], ['value' => $data['value']])
            );
        }

        $this->command->info('✅ Settings seeded: ' . count($settings) . ' entries across ' . count($groups) . ' groups.');
    }
}

