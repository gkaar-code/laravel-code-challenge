<?php declare(strict_types=1);
namespace App\Models\Traits;

use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyPosts
{
    public function posts() : HasMany
    {
        return $this->hasMany(Post::class, 'author_id', 'id');
    }
}
