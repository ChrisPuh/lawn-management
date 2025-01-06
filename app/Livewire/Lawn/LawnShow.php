<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/** @property ComponentContainer $form */
final class LawnShow extends Component implements HasForms
{
    use InteractsWithForms;

    public Lawn $lawn;

    public bool $isModalOpen = false;

    public ?array $data = [];

    /**
     * Mounts the component
     */
    public function mount(): void
    {
        $this->authorize('view', $this->lawn);
        $this->form->fill();
    }

    /**
     * The form for creating a new mowing record
     *
     * @todo Validate the form
     * @todo Extract the form into a separate class
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('mowed_on')
                    ->required()
                    ->label('Datum')
                    ->default(now()),

                TextInput::make('cutting_height')
                    ->label('Schnitthöhe')
                    ->placeholder('z.B. 4cm'),
            ])
            ->statePath('data');
    }

    /**
     * Opens the modal
     *
     * @todo extract the model opening and closing into a trait
     */
    public function openModal(): void
    {
        $this->isModalOpen = true;
    }

    /**
     * closes the modal
     *
     * @todo extract the model opening and closing into a trait
     */
    public function closeModal(): void
    {
        $this->isModalOpen = false;
    }

    /**
     * Creates a new mowing record
     *
     * @todo extract to action
     */
    public function create(): void
    {
        $this->authorize('manageMowingRecords', $this->lawn);

        $data = $this->form->getState();
        $this->lawn->mowingRecords()->create($data);
        $this->closeModal();
        $this->form->fill();
    }

    /**
     * Deletes the lawn and redirects to the lawn index
     */
    #[On('deleteConfirmed')]
    public function deleteLawn(): void
    {
        $this->authorize('delete', $this->lawn);

        $this->lawn->delete();
        $this->redirect(route('lawn.index'), navigate: true);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenfläche Details'])]
    public function render(): View
    {
        return view('livewire.lawn.lawn-show', [
            'lastMowingDate' => $this->lawn->getLastMowingDate(),
            'mowingRecords' => $this->lawn->mowingRecords()
                ->latest('mowed_on')
                ->get(),
        ]);
    }
}
