<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SettingService;
use Illuminate\Support\Facades\Schema;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        try {
            if (Schema::hasTable('settings')) {
                // Override Mail Config
                $useCustomSmtp = SettingService::get('mail.use_custom_smtp', false);
                if ($useCustomSmtp) {
                    config([
                        'mail.mailers.smtp.host' => SettingService::get('mail.host', '127.0.0.1'),
                        'mail.mailers.smtp.port' => SettingService::get('mail.port', 1025),
                        'mail.mailers.smtp.encryption' => SettingService::get('mail.encryption', 'tls'),
                        'mail.mailers.smtp.username' => SettingService::get('mail.username'),
                        'mail.mailers.smtp.password' => SettingService::get('mail.password'),
                        'mail.from.address' => SettingService::get('mail.from_email'),
                        'mail.from.name' => SettingService::get('mail.from_name'),
                    ]);
                }
                
                // Override App Name / Timezone
                config(['app.name' => SettingService::get('general.site_name', config('app.name'))]);
                config(['app.timezone' => SettingService::get('general.timezone', config('app.timezone'))]);
            }
        } catch (\Exception $e) {
            // Ignore if DB is not ready during setup or cache clears
        }
    }
}

