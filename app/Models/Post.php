<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public static function scopeVisibleForAuthenticated(Builder $query, User $user)
    {
        $query->where(function ($query) {
            $query->visibleForGuests();
        })->orWhere(function ($query) use ($user) {
            $query->onlyUnpublished()
            ;
        });
    }

    public static function scopeVisibleForGuests(Builder $query)
    {
        $query->onlyPublished();
    }

    public static function scopeOnlyPublished(Builder $query)
    {
        $query->where('is_published', '=', true);
    }

    public static function scopeOnlyUnpublished(Builder $query)
    {
        $query->where('is_published', '=', false);
    }
}
