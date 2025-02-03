<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\WaitingListStatus;
use App\Filament\Resources\WaitingListResource\Pages\CreateWaitingList;
use App\Filament\Resources\WaitingListResource\Pages\EditWaitingList;
use App\Filament\Resources\WaitingListResource\Pages\ListWaitingLists;
use App\Models\WaitingList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Pages\PageRegistration;

/**
 * @property-read WaitingList $record
 */
final class WaitingListResource extends Resource
{
    protected static ?string $model = WaitingList::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Warteliste';

    protected static ?string $modelLabel = 'Warteliste Eintrag';

    protected static ?string $pluralModelLabel = 'Warteliste Einträge';

    public static function form(Form $form): Form
    {
        $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Name'),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->label('E-Mail'),

            Select::make('status')
                ->options(array_reduce(
                    WaitingListStatus::cases(),
                    static function (array $carry, WaitingListStatus $status): array {
                        $carry[$status->value] = $status->label();
                        return $carry;
                    },
                    []
                ))
                ->required()
                ->label('Status'),

            Textarea::make('reason')
                ->maxLength(1000)
                ->label('Registrierungsgrund')
                ->columnSpanFull(),
        ]);

        return $form;
    }

    public static function table(Table $table): Table
    {
        $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Name'),

            TextColumn::make('email')
                ->searchable()
                ->sortable()
                ->label('E-Mail'),

            TextColumn::make('status')
                ->badge()
                ->color(fn(WaitingListStatus $state): string => $state->color())
                ->formatStateUsing(fn(WaitingListStatus $state): string => $state->label())
                ->label('Status'),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->label('Angemeldet am'),

            TextColumn::make('invited_at')
                ->dateTime()
                ->sortable()
                ->label('Eingeladen am')
                ->placeholder('Noch nicht eingeladen'),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_reduce(
                        WaitingListStatus::cases(),
                        static function (array $carry, WaitingListStatus $status): array {
                            $carry[$status->value] = $status->label();
                            return $carry;
                        },
                        []
                    ))
                    ->multiple()
                    ->label('Status filtern'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('invite')
                    ->icon('heroicon-o-paper-airplane')
                    ->label('Einladen')
                    ->action(function (WaitingList $record): void {
                        $record->update([
                            'status' => WaitingListStatus::Invited,
                            'invited_at' => Carbon::now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(WaitingList $record): bool => $record->status === WaitingListStatus::Pending),

                Tables\Actions\Action::make('register')
                    ->icon('heroicon-o-check-circle')
                    ->label('Registrieren')
                    ->action(function (WaitingList $record): void {
                        $record->update([
                            'status' => WaitingListStatus::Registered,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(WaitingList $record): bool => $record->status === WaitingListStatus::Invited),

                Tables\Actions\Action::make('decline')
                    ->icon('heroicon-o-x-circle')
                    ->label('Ablehnen')
                    ->color('danger')
                    ->action(function (WaitingList $record): void {
                        $record->update([
                            'status' => WaitingListStatus::Declined,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(WaitingList $record): bool => in_array($record->status, [WaitingListStatus::Pending, WaitingListStatus::Invited], true)
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');

        return $table;
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListWaitingLists::route('/'),
            'create' => CreateWaitingList::route('/create'),
            'edit' => EditWaitingList::route('/{record}/edit'),
        ];
    }
}
