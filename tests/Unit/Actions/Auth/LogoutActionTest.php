<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\LogoutAction;
use App\Contracts\Auth\LogoutActionInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

final class LogoutActionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private LogoutActionInterface $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->action = new LogoutAction;
    }

    public function test_logs_out_user(): void
    {
        Auth::login($this->user);
        $this->assertAuthenticated();

        $this->action->execute();

        $this->assertGuest();
    }

    public function test_cleans_up_session(): void
    {
        // Simulate a session with custom data
        session(['custom_key' => 'value']);

        // Bind session to the current request to match LogoutAction behavior
        app('request')->setLaravelSession(app('session.store'));

        // Execute the logout action
        $this->action->execute();

        // Assert the session data is cleared
        $this->assertFalse(session()->has('custom_key'), 'Session data was not cleared');
    }

    public function test_handles_request_with_response(): void
    {
        $request = Request::create('/logout', 'POST'); // Create a mock POST request
        $request->setLaravelSession(app('session.store')); // Attach the actual session store

        // Execute the handleRequest method
        $response = $this->action->handleRequest($request);

        // Assert the response status code
        $this->assertEquals(204, $response->getStatusCode(), 'Logout response code should be 204');
    }

    public function test_works_when_already_logged_out(): void
    {
        $this->assertGuest();

        $this->action->execute();

        $this->assertGuest();
    }
}
