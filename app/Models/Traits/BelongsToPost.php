<?php declare(strict_types=1);
namespace App\Models\Traits;

trait BelongsToPost
{
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}