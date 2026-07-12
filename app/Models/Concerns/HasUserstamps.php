<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasUserstamps
{
    public static function bootHasUserstamps(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && in_array('deleted_by', $model->getFillable()) || \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = Auth::id();
                // Avoid recursion
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            if (in_array('deleted_by', $model->getFillable()) || \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = null;
                $model->updated_by = Auth::check() ? Auth::id() : $model->updated_by;
                $model->saveQuietly();
            }
        });
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deleter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }
}
