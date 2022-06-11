<?php

namespace Tests\Feature;

use App\Http\Controllers\PostController;
use Illuminate\Http\Response;
use Tests\TestCase;

class AppIsReadyTest extends TestCase
{
    /**
     * Test the application is up and running.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_handles_5xx_exceptions_nicely()
    {
        // INFO: this endpoint needs DB. We are ensuring the
        //       migrations are not run in this test context.
        $this->getJson(action([PostController::class, 'index']))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => 'Something went wrong, please contact us.'
            ])
        ;
    }
}
