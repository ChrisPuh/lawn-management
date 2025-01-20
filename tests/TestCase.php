<?php

declare(strict_types=1);

namespace Tests;

use App\Actions\LawnCare\CreateFertilizingAction;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\Models\Lawn;
use App\Models\User;
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
        $this->action = new CreateFertilizingAction(
            app(LogLawnCareActionContract::class) // Ensure correct binding
        );

        // Create a user using the factory
        $this->user = User::factory()->create();

        // Create a lawn assigned to the user
        $this->lawn = Lawn::factory()->create([
            'user_id' => $this->user->id,
        ]);

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
