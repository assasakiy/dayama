<?php

declare(strict_types=1);

namespace Modules\System\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'system_settings';

    protected $guarded = [];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_env'    => 'boolean',
    ];

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
