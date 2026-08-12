<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\System\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'welcome-email',
                'name' => 'Email Selamat Datang',
                'subject' => 'Selamat datang di {{app_name}}!',
                'body' => '<h1>Halo, {{user_name}}!</h1><p>Terima kasih telah bergabung dengan {{app_name}}. Kami sangat senang Anda ada di sini.</p>',
                'variables' => ['app_name', 'user_name'],
                'is_active' => true,
            ],
            [
                'key' => 'reset-password',
                'name' => 'Reset Kata Sandi',
                'subject' => 'Reset kata sandi Anda untuk {{app_name}}',
                'body' => '<h1>Reset Kata Sandi</h1><p>Halo {{user_name}},</p><p>Anda meminta untuk mereset kata sandi. Klik tautan di bawah ini untuk meresetnya:</p><p><a href="{{reset_url}}">Reset Kata Sandi</a></p><p>Jika Anda tidak meminta ini, abaikan email ini.</p>',
                'variables' => ['app_name', 'user_name', 'reset_url'],
                'is_active' => true,
            ],
            [
                'key' => 'email-verify',
                'name' => 'Verifikasi Alamat Email',
                'subject' => 'Verifikasi email Anda untuk {{app_name}}',
                'body' => '<h1>Verifikasi Email</h1><p>Halo {{user_name}},</p><p>Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.</p><p><a href="{{verify_url}}">Verifikasi Alamat Email</a></p>',
                'variables' => ['app_name', 'user_name', 'verify_url'],
                'is_active' => true,
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
