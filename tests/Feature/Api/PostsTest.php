<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    const PUBLISHED_POSTS = 2;
    const UNPUBLISHED_POSTS = 3;

    /** @var \App\Models\User */
    protected $user;

    /** @var \Illuminate\Database\Eloquent\Collection<int,Post> */
    protected $publishedPosts;

    /** @var \Illuminate\Database\Eloquent\Collection<int,Post> */
    protected $unpublishedPosts;

    protected function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->publishedPosts = Post::factory(count: self::PUBLISHED_POSTS)
            ->published()
            ->create()
        ;

        $this->unpublishedPosts = Post::factory(count: self::UNPUBLISHED_POSTS)
            ->unpublished()
            ->authoredBy($this->user)
            ->create()
        ;
    }

    /** @test */
    public function a_list_of_posts_has_public_access()
    {
        $uri = route('posts.index');

        $this->getJson($uri)->assertSuccessful();
    }

    /** @test */
    public function a_guest_can_only_access_published_posts()
    {
        $uri = route('posts.index');
        // INFO: without authentication
        $this->getJson($uri)
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', length: self::PUBLISHED_POSTS);

            $this->publishedPosts->each(function ($post, $index) use ($json) {
                $json->has("data.{$index}", function ($json) use ($post) {
                    foreach ($post->toArray() as $attribute => $value) {
                        $json->where($attribute, $value);
                    }
                });
            });

            $json->etc();
        });
    }

    /** @test */
    public function an_authenticated_user_can_access_published_and_his_authored_unpublished_posts()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $uri = route('posts.index');

        $this->actingAs($this->user, 'sanctum')
        ->getJson($uri)
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', length: self::PUBLISHED_POSTS + self::UNPUBLISHED_POSTS);

            $this->publishedPosts->merge($this->unpublishedPosts)
            ->each(function ($post, $index) use ($json) {
                $json->has("data.{$index}", function ($json) use ($post) {
                    foreach ($post->toArray() as $attribute => $value) {
                        $json->where($attribute, $value);
                    }
                });
            });

            $json->etc();
        });
    }
}
