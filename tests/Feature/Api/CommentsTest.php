<?php

namespace Tests\Feature\Api;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    const PUBLISHED_COMMENTS = 2;
    const UNPUBLISHED_COMMENTS = 3;

    /** @var \App\Models\User */
    protected $user;

    /** @var \App\Models\Post */
    protected $publishedPostWithComments;

    protected function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->publishedPostWithComments = Post::factory()
            ->published()
            ->has(Comment::factory()
                ->published()
                ->count(self::PUBLISHED_COMMENTS)
            )
            ->has(Comment::factory()
                ->unpublished()
                ->authoredBy($this->user)
                ->count(self::UNPUBLISHED_COMMENTS)
            )
            ->create()
        ;
    }

    /** @test */
    public function a_list_of_comments_has_public_access()
    {
        $uri = route('comments.index');

        $this->getJson($uri)->assertSuccessful();
    }

    /** @test */
    public function a_guest_can_only_access_published_comments()
    {
        $post = $this->publishedPostWithComments;

        $uri = route('comments.index', compact('post'));
        $this->getJson($uri)
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', length: self::PUBLISHED_COMMENTS);

                $this->publishedPostWithComments->comments
                ->filter->is_published
                ->each(function ($comment, $index) use ($json) {
                    $json->has("data.{$index}", function ($json) use ($comment) {
                        foreach ($comment->toArray() as $attribute => $value) {
                                $json->where($attribute, $value);
                        }
                    });
                });

                $json->etc();
            })
        ;
    }

    /** @test */
    public function an_authenticated_user_can_access_published_and_his_authored_unpublished_comments()
    {
        $post = $this->publishedPostWithComments;
        $uri = route('comments.index', compact('post'));

        $this->actingAs($this->user)
        ->getJson($uri)
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', length: self::PUBLISHED_COMMENTS + self::UNPUBLISHED_COMMENTS);

            $this->publishedPostWithComments->comments
            ->each(function ($comment, $index) use ($json) {
                $json->has("data.{$index}", function ($json) use ($comment) {
                    foreach ($comment->toArray() as $attribute => $value) {
                        $json->where($attribute, $value);
                    }
                });
            });

            $json->etc();
        });
    }
}
