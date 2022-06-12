<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();
    }

    /** @test */
    public function a_list_of_comments_has_public_access()
    {
        $post = Post::factory()->published()
            ->has(Comment::factory()->count(3))
            ->create()
        ;

        $uri = route('comments.index', compact('post'));
        $this->getJson($uri)
            ->assertSuccessful()
            ->assertJson($post->toArray())
        ;

        $this->markTestIncomplete();
    }
}
