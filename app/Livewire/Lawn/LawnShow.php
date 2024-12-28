<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Reactive;
use Livewire\Component;

final class LawnShow extends Component implements HasForms
{
    use InteractsWithForms;

    public Lawn $lawn;

    public bool $isModalOpen = false;

    public bool $showDeleteModal = false;

    public ?array $data = [];

    #[Reactive]
    public ?Form $form = null;

    /**
     * Mounts the component
     */
    public function mount(): void
    {
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
        $data = $this->form->getState();

        $this->lawn->mowingRecords()->create($data);

        $this->closeModal();
        $this->form->fill();
    }

    /**
     * confirms the deletion of the lawn
     * deletes the lawn and redirects to the lawn index
     *
     * @todo extract to action
     */
    public function confirmDelete(): void
    {
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
            'deleteModalTitle' => 'Rasenfläche löschen',
            'deleteModalMessage' => "Möchten Sie die Rasenfläche \"{$this->lawn->name}\" wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.",
        ]);
    }
}
