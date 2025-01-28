<?php

declare(strict_types=1);

namespace App\Filament\Resources\WaitlistResource\Pages;

use App\Filament\Resources\WaitlistResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateWaitlist extends CreateRecord
{
    protected static string $resource = WaitlistResource::class;
}
