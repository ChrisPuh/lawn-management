<?php

namespace Feature\Controller;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class PageControllerTest extends TestCase
{
    #[Test]
    public function landing_pages_are_accessible()
    {
        $routes = ['welcome', 'about', 'features', 'privacy', 'terms', 'contact'];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
            $response->assertViewIs('landing.' . $route);
        }
    }

    #[Test]
    public function dashboard_link_only_visible_when_authenticated()
    {
        $response = $this->get('/');
        $response->assertDontSee('Dashboard');

        $this->actingAsUser();
        $response = $this->get('/');
        $response->assertSee('Dashboard');
    }

    #[Test]
    public function cache_headers_are_present()
    {
        $response = $this->get(route('privacy'));
        $response->assertHeader('Cache-Control', 'max-age=3600, public');
    }

    #[Test]
    public function auth_buttons_switch_correctly()
    {
        $response = $this->get('/');
        $response->assertSee('Login')->assertSee('Register');

        $this->actingAsUser();
        $response = $this->get('/');
        $response->assertSee('Logout')->assertDontSee('Login');
    }

    private function actingAsUser()
    {
        return $this->actingAs(\App\Models\User::factory()->create());
    }
}
