<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

class IdentityService
{
    protected static ?array $resolved = null;

    /**
     * Get the current resolved visitor identity.
     * Caches the result statically per request.
     */
    public static function current(): array
    {
        return static::$resolved ??= static::resolve();
    }

    /**
     * Resolves the identity from the current request.
     */
    protected static function resolve(): array
    {
        $visitorToken = self::getOrCreateVisitorToken();
        $user = auth()->user();

        if ($user) {
            return [
                'type' => 'user',
                'key' => "user:{$user->id}",
                'user_id' => $user->id,
                'visitor_token' => $visitorToken,
            ];
        }

        return [
            'type' => 'guest',
            'key' => "guest:{$visitorToken}",
            'user_id' => null,
            'visitor_token' => $visitorToken,
        ];
    }

    /**
     * Get or create a visitor token.
     */
    public static function getOrCreateVisitorToken(): string
    {
        if ($token = request()->cookie('visitor_token')) {
            return $token;
        }

        // Generate UUIDv7 if PHP supports it natively in Str::uuid() or just orderedUuid
        return (string) Str::uuid();
    }
}
