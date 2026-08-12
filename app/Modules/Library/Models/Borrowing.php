<?php

declare(strict_types=1);

namespace Modules\Library\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    use HasUuid;

    protected $table = 'library_borrowings';

    protected $fillable = [
        'book_id',
        'borrower_type',
        'borrower_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'notes',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
