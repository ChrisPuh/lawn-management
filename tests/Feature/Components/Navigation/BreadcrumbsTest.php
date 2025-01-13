<?php

declare(strict_types=1);

namespace Tests\Feature\Components\Navigation;

use App\Livewire\Components\Navigation\Breadcrumbs;
use App\Models\Lawn;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class BreadcrumbsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_profile_breadcrumbs(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $this->get(route('profile.index'))
            ->assertOk();

        Livewire::withQueryParams(['_route' => 'profile.index'])
            ->test(Breadcrumbs::class)
            ->assertSee('Profil');
    }

    #[Test]
    public function it_shows_profile_edit_breadcrumbs(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $this->get(route('profile.edit'))
            ->assertOk();

        Livewire::withQueryParams(['_route' => 'profile.edit'])
            ->test(Breadcrumbs::class)
            ->assertSee('Profil')
            ->assertSee('Bearbeiten');
    }

    #[Test]
    public function it_shows_dashboard_breadcrumbs(): void
    {
        $user = User::factory()->create();
        /** @var Authenticatable $user */
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertOk();

        Livewire::withQueryParams(['_route' => 'dashboard'])
            ->test(Breadcrumbs::class)
            ->assertSee('Dashboard');
    }

    #[Test]
    public function it_shows_lawn_breadcrumbs_with_lawn_name(): void
    {
        $lawn = Lawn::factory()->create(['name' => 'Vorgarten']);

        Livewire::withQueryParams([
            '_route' => 'lawn.show',
            'lawn' => $lawn->id,
        ])
            ->test(Breadcrumbs::class)
            ->assertSee('Rasenflächen')
            ->assertSee('Vorgarten');
    }
}
