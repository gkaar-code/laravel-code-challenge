<?php declare(strict_types=1);
namespace App\Models\Traits;

use App\Models\Comment;

trait HasManyComments
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
