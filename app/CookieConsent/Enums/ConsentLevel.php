<?php

namespace App\CookieConsent\Enums;

enum ConsentLevel: string
{
    case ALL = 'all';
    case NECESSARY = 'necessary';
    case NONE = 'none'; // Basically declined all optional ones, same as necessary in simple setups but distinct for UI
}
