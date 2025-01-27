<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controller;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PageControllerTest extends TestCase
{
    #[Test]
    public function landing_pages_are_accessible(): void
    {
        $routes = ['welcome', 'about', 'features', 'privacy', 'terms', 'contact'];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
            $response->assertViewIs('landing.'.$route);
        }
    }

    #[Test]
    public function dashboard_link_only_visible_when_authenticated(): void
    {
        $response = $this->get('/');
        $response->assertDontSee('Dashboard');

        $this->actingAsUser();
        $response = $this->get('/');
        $response->assertSee('Dashboard');
    }

    #[Test]
    public function auth_buttons_switch_correctly(): void
    {
        $response = $this->get('/');
        $response->assertSee('Login')->assertSee('Register');

        $this->actingAsUser();
        $response = $this->get('/');
        $response->assertSee('Logout')->assertDontSee('Login');
    }

    #[Test]
    public function cache_headers_are_present(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertHeader(
            'Cache-Control',
            'max-age=0, must-revalidate, no-cache, no-store, private'
        );
    }

    private function actingAsUser()
    {
        return $this->actingAs(\App\Models\User::factory()->create());
    }
}
