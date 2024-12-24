<?php


namespace Tests\Feature\Components\Navigation;


use App\Livewire\Components\Navigation\Breadcrumbs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BreadcrumbsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_profile_breadcrumbs()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('profile.index'))
            ->assertOk();

        Livewire::withQueryParams(['_route' => 'profile.index'])
            ->test(Breadcrumbs::class)
            ->assertSee('Profil');
    }

    #[Test]
    public function it_shows_profile_edit_breadcrumbs()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('profile.edit'))
            ->assertOk();

        Livewire::withQueryParams(['_route' => 'profile.edit'])
            ->test(Breadcrumbs::class)
            ->assertSee('Profil')
            ->assertSee('Bearbeiten');
    }

    #[Test]
    public function it_shows_dashboard_breadcrumbs()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('dashboard'))
            ->assertOk();

        Livewire::withQueryParams(['_route' => 'dashboard'])
            ->test(Breadcrumbs::class)
            ->assertSee('Dashboard');
    }

    #[Test]
    public function it_shows_lawn_breadcrumbs_with_lawn_name()
    {
        // $user = User::factory()->create();
        // $lawn = Lawn::factory()->create(['name' => 'Vorgarten']);

        // $this->actingAs($user);

        // $this->get(route('lawns.show', $lawn))
        //     ->assertOk();

        // Livewire::withQueryParams([
        //     '_route' => 'lawns.show',
        //     'lawn' => $lawn->id
        // ])
        //     ->test(Breadcrumbs::class)
        //     ->assertSee('Rasenflächen')
        //     ->assertSee('Vorgarten');

        $this->markTestSkipped('Implement this test, when the lawn model is available.');
    }
}
