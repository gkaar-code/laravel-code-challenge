<?php declare(strict_types=1);
namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuthorship
{
    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', $this->getKeyName());
    }

    public static function scopeAuthoredBy(Builder $query, User $user)
    {
        $query->whereHas('author', function ($query) use ($user) {
            $query->whereKey($user->getKey());
        });
    }
}
