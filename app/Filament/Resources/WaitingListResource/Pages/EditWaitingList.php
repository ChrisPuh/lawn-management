<?php

declare(strict_types=1);

namespace App\Filament\Resources\WaitingListResource\Pages;

use App\Filament\Resources\WaitingListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditWaitingList extends EditRecord
{
    protected static string $resource = WaitingListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
