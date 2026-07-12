<?php

namespace App\CookieConsent;

use App\CookieConsent\Enums\ConsentLevel;
use App\CookieConsent\Events\CookieConsentUpdated;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class CookieConsentService
{
    public const COOKIE_NAME = 'cookie_consent_level';
    public const COOKIE_LIFETIME = 60 * 24 * 365; // 1 year

    public function acceptAll(): void
    {
        $this->setLevel(ConsentLevel::ALL);
    }

    public function acceptNecessary(): void
    {
        $this->setLevel(ConsentLevel::NECESSARY);
    }

    public function setLevel(ConsentLevel $level): void
    {
        Cookie::queue(self::COOKIE_NAME, $level->value, self::COOKIE_LIFETIME);
        
        CookieConsentUpdated::dispatch($level, Request::ip());
    }

    public function level(): ?ConsentLevel
    {
        $value = Cookie::get(self::COOKIE_NAME);
        return $value ? ConsentLevel::tryFrom($value) : null;
    }

    public function forget(): void
    {
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));
    }

    public function isAccepted(): bool
    {
        return $this->level() === ConsentLevel::ALL;
    }

    public function isNecessaryOnly(): bool
    {
        return $this->level() === ConsentLevel::NECESSARY;
    }

    public function hasConsent(ConsentLevel $level): bool
    {
        if ($this->level() === ConsentLevel::ALL) {
            return true; // ALL encompasses everything
        }

        return $this->level() === $level;
    }
}
