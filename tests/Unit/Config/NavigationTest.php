<?php
// tests/Unit/Config/NavigationTest.php
namespace Tests\Unit\Config;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    #[Test]
    public function navigation_config_has_required_structure()
    {
        $config = config('navigation.breadcrumbs.segments');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('profile.index', $config);
        $this->assertArrayHasKey('profile.edit', $config);
        $this->assertArrayHasKey('lawns.index', $config);
        $this->assertArrayHasKey('lawns.show', $config);
    }

    #[Test]
    public function breadcrumb_segments_have_required_keys()
    {
        $segments = config('navigation.breadcrumbs.segments');

        foreach ($segments as $route => $routeSegments) {
            foreach ($routeSegments as $key => $segment) {
                $this->assertArrayHasKey('label', $segment);
                $this->assertArrayHasKey('route', $segment);
            }
        }
    }
}