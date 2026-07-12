<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_env'    => 'boolean',
    ];

    /**
     * Get and set the value attribute with encryption and dynamic type casting.
     */
    protected function value(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($value === null) {
                    return null;
                }

                try {
                    $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($value);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    // Fallback to raw value if it was saved as plain text previously
                    $decrypted = $value;
                }

                $type = $attributes['type'] ?? 'string';
                return match ($type) {
                    'boolean' => filter_var($decrypted, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $decrypted,
                    'json', 'array' => json_decode($decrypted, true),
                    default => $decrypted,
                };
            },
            set: function (mixed $value) {
                if ($value === null) {
                    return null;
                }

                $stringVal = is_array($value) || is_object($value)
                    ? json_encode($value)
                    : (string) $value;

                return \Illuminate\Support\Facades\Crypt::encryptString($stringVal);
            }
        );
    }
}


