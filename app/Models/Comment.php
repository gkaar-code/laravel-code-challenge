<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Traits\BelongsToPost;
use App\Models\Traits\HasAuthorship;
use App\Models\Traits\IsPublishable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function storeComment(array $attributes, Post $post, User $author) : static
    {
        return DB::transaction(function () use ($attributes, $post, $author) {

            $comment = new static($attributes);

            $comment->post()->associate($post);
            $comment->author()->associate($author);

            $comment->push();

            return $comment;
        });
    }
}
