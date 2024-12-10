<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterUserActionTest extends TestCase
{
    use RefreshDatabase;

    private RegisterUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RegisterUserAction();
    }

    #[Test]
    public function it_creates_a_user(): void
    {
        Event::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->register($data);

        // Assert user was created with correct data
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($data['name'], $user->name);
        $this->assertSame($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password));

        // Assert user exists in database
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    #[Test]
    public function it_hashes_the_password(): void
    {
        Event::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->register($data);

        $this->assertNotSame($data['password'], $user->password);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    #[Test]
    public function it_dispatches_registered_event(): void
    {
        Event::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->register($data);

        Event::assertDispatched(Registered::class, fn ($event) => $event->user->id === $user->id);
    }

    #[Test]
    public function it_logs_in_the_user(): void
    {
        Event::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->register($data);

        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    #[Test]
    public function it_returns_created_user(): void
    {
        Event::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->id);
        $this->assertTrue($user->exists);
    }

    #[Test]
    public function it_creates_users_with_unique_emails(): void
    {
        Event::fake();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Create first user
        $this->action->register($data);

        // Attempt to create second user with same email
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->action->register($data);
    }

    #[Test]
    #[DataProvider('invalidDataProvider')]
    public function it_validates_required_data(array $data): void
    {
        Event::fake();

        $this->expectException(\ErrorException::class);
        $this->action->register($data);
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing name' => [
                [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ],
            ],
            'missing email' => [
                [
                    'name' => 'Test User',
                    'password' => 'password123',
                ],
            ],
            'missing password' => [
                [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ],
        ];
    }
}
