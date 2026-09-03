<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InstitutionMembership extends Model
{
    use HasUuids;

    protected $table = 'core_institution_memberships';

    protected $fillable = [
        'id',
        'person_id',
        'institution_id',
        'status',
        'joined_at',
        'left_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'left_at'   => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    /**
     * Pastikan Person memiliki membership aktif di lembaga tersebut.
     * Jika belum ada, buat baru.
     * Jika sudah ada tapi inactive, reaktivasi tanpa merusak joined_at lama.
     */
    public static function ensureMembership(string $personId, string $institutionId): self
    {
        $membership = self::where('person_id', $personId)
            ->where('institution_id', $institutionId)
            ->first();

        if (! $membership) {
            return self::create([
                'id'             => (string) Str::orderedUuid(),
                'person_id'      => $personId,
                'institution_id' => $institutionId,
                'status'         => 'active',
                'joined_at'      => now()->toDateString(),
                'left_at'        => null,
            ]);
        }

        if ($membership->status !== 'active') {
            $membership->update([
                'status'  => 'active',
                'left_at' => null,
            ]);
        }

        return $membership;
    }
}
