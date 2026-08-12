<?php

declare(strict_types=1);

namespace Modules\Library\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookAuthor extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_book_authors';

    protected $fillable = [
        'name',
        'slug',
        'biography',
        'photo',
        'is_active',
    ];
}
