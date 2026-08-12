<?php
declare(strict_types=1);
namespace Modules\Core\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MaritalStatus extends Model
{
    use HasUuid, SoftDeletes;
    protected $table = 'core_marital_statuses';
    protected $fillable = ['name', 'slug', 'sort_order', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean', 'sort_order' => 'integer']; }
}
