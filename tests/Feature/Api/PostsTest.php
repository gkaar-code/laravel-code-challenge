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

    /** @test */
    public function a_guest_can_only_access_details_of_a_published_post()
    {
        $post = $this->publishedPosts->first();
        $uri = route('posts.show', compact('post'));

        $this->getJson($uri)
            ->assertSuccessful()
        ;

        $post = $this->unpublishedPosts->first();
        $uri = route('posts.show', compact('post'));

        $this->getJson($uri)
            ->assertForbidden()
        ;
    }

    /** @test */
    public function an_authenticated_user_can_access_any_post_authored_by_him()
    {
        $post = Post::factory()
            ->unpublished()
            ->create()
        ;

        $uri = route('posts.show', compact('post'));

        $this->actingAs($this->user)
            ->getJson($uri)
            ->assertForbidden()
        ;

        $post = Post::factory()
            ->unpublished()
            ->authoredBy($this->user)
            ->create()
        ;

        $uri = route('posts.show', compact('post'));

        $this->actingAs($this->user)
            ->getJson($uri)
            ->assertSuccessful()
        ;
    }

    /** @test */
    public function only_authenticated_users_can_create_new_posts()
    {
        $uri = route('posts.store');

        $this->postJson($uri, $attributes = [
                'title' => 'A new Post Title',
                'content' => 'Some dummy content',
            ])
            // INFO: middleware fails because user must be authenticated.
            ->assertUnauthorized()
        ;

        $this->assertDatabaseMissing('posts', $attributes);

        $this->actingAs($this->user)
            ->postJson($uri, $attributes = [
                'title' => 'A new Post Title',
                'content' => 'Some dummy content',
            ])
            ->assertCreated()
            // INFO: ensure the newly created post is returned.
            ->assertJson(function (AssertableJson $json) use ($attributes) {
                $json->where('title', $attributes['title'])
                     ->where('content', $attributes['content'])
                     ->etc()
                ;
            })
        ;

        $this->assertDatabaseHas('posts', $attributes);
    }

    /** @test */
    public function only_authenticated_users_can_update_their_posts()
    {
        $post = Post::factory()->create();

        $uri = route('posts.update', compact('post'));

        $this->putJson($uri, $attributes = [
                'title' => 'A MODIFIED Post Title',
                'content' => 'Some MODIFIED dummy content',
            ])
            // INFO: middleware fails because user must be authenticated.
            ->assertUnauthorized()
        ;

        $this->assertDatabaseMissing('posts', $attributes);

        $post = Post::factory()->authoredBy($this->user)->create();

        $uri = route('posts.update', compact('post'));

        $this->actingAs($this->user)
            ->putJson($uri, $attributes = [
                'title' => 'Another MODIFIED Post Title',
                'content' => 'Some other MODIFIED dummy content',
            ])
            ->assertSuccessful()
            // INFO: ensure the newly created post is returned.
            ->assertJson(function (AssertableJson $json) use ($attributes) {
                $json->where('title', $attributes['title'])
                     ->where('content', $attributes['content'])
                     ->etc()
                ;
            })
        ;

        $this->assertDatabaseHas('posts', $attributes);
    }
}
