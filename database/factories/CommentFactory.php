<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'author_id' => User::factory()->lazy(),
            'content' => $this->faker->paragraphs(nb: 3, asText: true),
            // INFO: a comment can only be published if the post it belongs
            //       has already been published:
            //       You shouldn't even be able to add a comment to an unpublished post!
            'is_published' => $published = $this->faker->boolean(),
            'post_id' => ($published)
                ? Post::factory()->published()->lazy()
                : Post::factory()->lazy()
            ,
        ];
    }

    /**
     * Indicate the post is published.
     *
     * @return static
     */
    public function published()
    {
        return $this->state(function ($attributes) {
            return [
                'is_published' => true,
                'post_id' => Post::factory()->published()->lazy(),
            ];
        });
    }

    /**
     * Indicate the post is not published.
     *
     * @return static
     */
    public function unpublished()
    {
        return $this->state(function ($attributes) {
            return [
                'is_published' => false,
                'post_id' => Post::factory()->published()->lazy(),
            ];
        });
    }

    /**
     * Indicate the author of the post.
     *
     * @param User $user
     *
     * @return static
     */
    public function authoredBy(User $user)
    {
        return $this->state(function ($attributes) use ($user) {
            return [
                'author_id' => $user->getKey(),
            ];
        });
    }
}
