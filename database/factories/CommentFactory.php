<?php

namespace Database\Factories;

use App\Models\Post;
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
            'post_id' => Post::factory()->lazy(),
            'content' => $this->faker->paragraphs(nb: 3, asText: true),
            'is_published' => $this->faker->boolean(),
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
                'is_published' => true
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
                'is_published' => false
            ];
        });
    }
}
