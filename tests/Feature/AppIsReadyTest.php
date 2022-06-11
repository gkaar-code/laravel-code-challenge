<?php

namespace Tests\Feature;

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
}
