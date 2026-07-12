<?php

namespace App\CookieConsent\Events;

use App\CookieConsent\Enums\ConsentLevel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CookieConsentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ConsentLevel $level;
    public ?string $ipAddress;

    public function __construct(ConsentLevel $level, ?string $ipAddress = null)
    {
        $this->level = $level;
        $this->ipAddress = $ipAddress;
    }
}
