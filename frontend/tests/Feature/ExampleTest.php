<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root URL redirects to login page.
     */
    public function test_the_application_redirects_root_to_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $this->withoutVite();
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
