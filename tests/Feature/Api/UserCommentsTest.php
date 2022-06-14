<?php

namespace Tests\Feature\Api;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserCommentsTest extends TestCase
{
    use RefreshDatabase;

    const PETER_PUBLISHED_COMMENTS = 2;
    const JOHN_UNPUBLISHED_COMMENTS = 3;
    const JOHN_PUBLISHED_COMMENTS = 4;

    /** @var \App\Models\User */
    protected $userJohn;

    /** @var \App\Models\User */
    protected $userPeter;

    /** @var \App\Models\Post */
    protected $publishedPostWithComments;

    protected function setUp() : void
    {
        parent::setUp();

        $this->userJohn = User::factory()->create();

        Comment::factory()
            ->unpublished()
            ->count(self::JOHN_UNPUBLISHED_COMMENTS)
            ->authoredBy($this->userJohn)
            ->create()
        ;

        Comment::factory()
            ->published()
            ->count(self::JOHN_PUBLISHED_COMMENTS)
            ->authoredBy($this->userJohn)
            ->create()
        ;

        $this->userPeter = User::factory()->create();

        Comment::factory()
            ->published()
            ->count(self::PETER_PUBLISHED_COMMENTS)
            ->authoredBy($this->userPeter)
            ->create()
        ;
    }

    /** @test */
    public function a_guest_can_view_published_comments_authored_by_any_user()
    {
        $user = $this->userJohn;

        $uri = route('users.comments.index', compact('user'));


        // INFO: Unauthenticated user wants to see John's coments.
        $this->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::JOHN_PUBLISHED_COMMENTS)
                ->etc()
            )
        ;

        $user = $this->userPeter;

        $uri = route('users.comments.index', compact('user'));

        // INFO: Unauthenticated user wants to see Peter's coments.
        $this->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::PETER_PUBLISHED_COMMENTS)
                ->etc()
            )
        ;
    }

    /** @test */
    public function an_authenticated_user_can_view_published_comments_and_those_authored_by_him()
    {
        $user = $this->userPeter;

        // INFO: John wants to see Peter's comments .
        $uri = route('users.comments.index', compact('user'));

        $this->actingAs($this->userJohn)
            ->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::PETER_PUBLISHED_COMMENTS)
                ->etc()
            )
        ;

        // INFO: John wants to see his own comments.
        $user = $this->userJohn;

        $uri = route('users.comments.index', compact('user'));

        $this->actingAs($this->userJohn)
            ->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::JOHN_PUBLISHED_COMMENTS + self::JOHN_UNPUBLISHED_COMMENTS)
                ->etc()
            )
        ;
    }
}
