<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\WaitlistStatus;
use App\Filament\Resources\WaitlistResource\Pages;
use App\Models\Waitlist;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

final class WaitlistResource extends Resource
{
    protected static ?string $model = Waitlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Warteliste';

    protected static ?string $modelLabel = 'Warteliste Eintrag';

    protected static ?string $pluralModelLabel = 'Warteliste Einträge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->options(array_reduce(WaitlistStatus::cases(), function ($carry, $status) {
                        $carry[$status->value] = $status->label();

                        return $carry;
                    }, []))
                    ->required()
                    ->label('Status'),

                Textarea::make('reason')
                    ->maxLength(1000)
                    ->label('Registrierungsgrund')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->color(fn (WaitlistStatus $state) => $state->color())
                    ->formatStateUsing(fn (WaitlistStatus $state) => $state->label())
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
                    ->options(array_reduce(WaitlistStatus::cases(), function ($carry, $status) {
                        $carry[$status->value] = $status->label();

                        return $carry;
                    }, []))
                    ->label('Status filtern'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('invite')
                    ->icon('heroicon-o-paper-airplane')
                    ->label('Einladen')
                    ->action(function (Waitlist $record): void {
                        $record->update([
                            'status' => WaitlistStatus::Invited,
                            'invited_at' => Carbon::now(),
                        ]);
                    })
                    ->visible(fn (Waitlist $record) => $record->status === WaitlistStatus::Pending),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWaitlists::route('/'),
            'create' => Pages\CreateWaitlist::route('/create'),
            'edit' => Pages\EditWaitlist::route('/{record}/edit'),
        ];
    }
}
