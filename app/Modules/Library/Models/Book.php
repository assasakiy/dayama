<?php

declare(strict_types=1);

namespace Modules\Library\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_books';

    protected $fillable = [
        'title',
        'slug',
        'isbn',
        'author_id',
        'category_id',
        'publisher',
        'published_year',
        'pages',
        'description',
        'cover_image',
        'quantity',
        'available_quantity',
        'location',
        'is_active',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(BookAuthor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class);
    }
}
