<?php

declare(strict_types=1);

namespace Tests;

use App\Actions\LawnCare\CreateFertilizingAction;
use App\Models\Lawn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    private Lawn $lawn;

    private CreateFertilizingAction $action;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        // Your cleanup code
    }
}
