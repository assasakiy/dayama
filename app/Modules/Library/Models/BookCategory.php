<?php

declare(strict_types=1);

namespace Modules\Library\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'library_book_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
    ];
}
