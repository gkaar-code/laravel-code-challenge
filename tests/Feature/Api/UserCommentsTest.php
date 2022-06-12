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

    const OTHER_PUBLISHED_COMMENTS = 2;
    const OWN_UNPUBLISHED_COMMENTS = 3;
    const OWN_PUBLISHED_COMMENTS = 4;

    /** @var \App\Models\User */
    protected $loggedUser;

    /** @var \App\Models\User */
    protected $user;

    /** @var \App\Models\Post */
    protected $publishedPostWithComments;

    protected function setUp() : void
    {
        parent::setUp();
        $this->loggedUser = User::factory()->create();

        $this->user = User::factory()
            ->has(Comment::factory()->published()->count(self::OTHER_PUBLISHED_COMMENTS))
            ->has(Comment::factory()->unpublished()->count(self::OWN_UNPUBLISHED_COMMENTS)->authoredBy($this->loggedUser))
            ->has(Comment::factory()->published()->count(self::OWN_PUBLISHED_COMMENTS)->authoredBy($this->loggedUser))
            ->create()
        ;

    }

    /** @test */
    public function a_guest_can_view_published_comments_authored_by_any_user()
    {
        $user = $this->user;

        $uri = route('users.comments.index', compact('user'));
        $this->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::OTHER_PUBLISHED_COMMENTS + self::OWN_PUBLISHED_COMMENTS)
                ->etc()
            )
        ;
    }

    /** @test */
    public function an_authenticated_user_can_view_published_comments_and_those_authored_by_him()
    {
        $user = $this->user;

        $uri = route('users.comments.index', compact('user'));
        $this->actingAs($this->loggedUser)
            ->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::OTHER_PUBLISHED_COMMENTS + self::OWN_PUBLISHED_COMMENTS)
                ->etc()
            )
        ;

        // INFO: when being the same user, protected comments can be seen too.
        $user = $this->loggedUser;

        $uri = route('users.comments.index', compact('user'));
        $this->actingAs($this->loggedUser)
            ->getJson($uri)
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', self::OTHER_PUBLISHED_COMMENTS + self::OWN_PUBLISHED_COMMENTS + self::OWN_UNPUBLISHED_COMMENTS)
                ->etc()
            )
        ;

        $this->markTestIncomplete();
    }
}
