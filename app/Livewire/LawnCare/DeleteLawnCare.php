<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Contracts\LawnCare\DeleteLawnCareActionContract;
use App\Models\LawnCare;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

final class DeleteLawnCare extends Component
{
    public bool $showModal = false;

    public ?LawnCare $care = null;

    #[On('confirm-delete-care')]
    public function confirm(LawnCare $care): void
    {
        $this->care = $care;
        $this->showModal = true;
    }

    public function delete(): void
    {
        try {
            $this->dispatch('care-details-closed');

            app(DeleteLawnCareActionContract::class)->execute($this->care, Auth::id());

            $this->dispatch('care-recorded');
            $this->cancel();
        } catch (Exception $e) {
            $this->addError('deletion', 'Fehler beim Löschen: ' . $e->getMessage());
        }
    }

    public function cancel(): void
    {
        $this->showModal = false;
        $this->care = null;
    }

    public function render()
    {
        return view('livewire.lawn-care.delete-lawn-care');
    }
}
