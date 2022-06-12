<?php declare(strict_types=1);
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait IsPublishable
{
    public static function scopeOnlyPublished(Builder $query)
    {
        $query->where($query->qualifyColumn('is_published'), '=', true);
    }

    public static function scopeOnlyUnpublished(Builder $query)
    {
        $query->where($query->qualifyColumn('is_published'), '=', false);
    }
}
