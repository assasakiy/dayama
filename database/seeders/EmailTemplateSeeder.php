<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

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
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{app_name}}!',
                'body' => '<h1>Welcome, {{user_name}}!</h1><p>Thanks for joining {{app_name}}. We are thrilled to have you here.</p>',
                'variables' => ['app_name', 'user_name'],
                'is_active' => true,
            ],
            [
                'key' => 'reset-password',
                'name' => 'Reset Password',
                'subject' => 'Reset your password for {{app_name}}',
                'body' => '<h1>Password Reset</h1><p>Hello {{user_name}},</p><p>You requested a password reset. Click the link below to reset it:</p><p><a href="{{reset_url}}">Reset Password</a></p><p>If you did not request this, please ignore this email.</p>',
                'variables' => ['app_name', 'user_name', 'reset_url'],
                'is_active' => true,
            ],
            [
                'key' => 'email-verify',
                'name' => 'Verify Email Address',
                'subject' => 'Verify your email for {{app_name}}',
                'body' => '<h1>Verify Email</h1><p>Hello {{user_name}},</p><p>Please click the button below to verify your email address.</p><p><a href="{{verify_url}}">Verify Email Address</a></p>',
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
