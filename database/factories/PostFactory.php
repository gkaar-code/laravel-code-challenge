<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $title = $this->faker->words(6, asText: true),
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, asText: true),
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
