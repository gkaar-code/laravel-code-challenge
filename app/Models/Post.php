<?php

namespace App\Models;

use App\Models\Traits\HasAuthorship;
use App\Models\Traits\HasManyComments;
use App\Models\Traits\IsPublishable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasAuthorship,
        HasManyComments,
        IsPublishable,
        HasFactory
    ;

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected $fillable = [
        'title',
        'content',
    ];

    public static function booted()
    {
        static::saving(function (self $post) {
            $post->slug = $post->title;
        });
    }

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

    public static function storePost(array $attributes, User $author) : static
    {
        return DB::transaction(function () use ($attributes, $author) {

            $post = new static($attributes);

            $post->author()->associate($author);

            $post->push();

            return $post;
        });
    }

    public function updatePost(array $attributes) : static
    {
        return DB::transaction(function () use ($attributes) {
            $this->fill($attributes);

            $this->save();

            return $this;
        });
    }

    public function deletePost() : static
    {
        return DB::transaction(function () {
            $this->delete();

            return $this;
        });
    }

    public function slug() : Attribute
    {
        return new Attribute(
            set: fn ($value) => Str::slug($value),
        );
    }
}
