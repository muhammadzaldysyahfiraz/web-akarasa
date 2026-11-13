<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_homepage_redirects_to_the_public_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('user.dashboard'));
        $this->get(route('user.dashboard'))->assertOk()->assertSee('Dashboard');
    }
}
