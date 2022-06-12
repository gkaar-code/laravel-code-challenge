<?php

namespace App\Models;

use App\Models\Traits\BelongsToPost;
use App\Models\Traits\HasAuthorship;
use App\Models\Traits\IsPublishable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use BelongsToPost,
        HasAuthorship,
        IsPublishable,
        HasFactory
    ;

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected $fillable = [
        'content',
    ];

    public static function scopeVisibleForAuthenticated(Builder $query, User $user)
    {
        $query->where(function ($query) {
            $query->visibleForGuests();
        })->orWhere(function ($query) use ($user) {
            $query->onlyUnpublished()
                  ->authoredBy($user)
            ;
        });
    }

    public static function scopeVisibleForGuests(Builder $query)
    {
        $query->onlyPublished();
    }
}
