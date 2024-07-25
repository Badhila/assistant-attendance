<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\RelationManagers\PeriodsRelationManager;
use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('code')
                    ->label('Code')
                    ->options([
                        'P' => 'P',
                        'AK' => 'AK',
                        'AP' => 'AP',
                        'J' => 'J',
                        'SI' => 'SI',
                    ])
                    ->required()
                    ->placeholder('Select the room code')
                    ->native(false),
                Select::make('name')
                    ->label('Name')
                    ->options([
                        'Pemrograman' => 'Pemrograman',
                        'Aplikasi Komputasi' => 'Aplikasi Komputasi',
                        'Aplikasi Profesional' => 'Aplikasi Profesional',
                        'Jaringan Komputer' => 'Jaringan Komputer',
                        'Sistem Informasi' => 'Sistem Informasi',
                    ])
                    ->required()
                    ->placeholder('Select the room name')
                    ->native(false),
                Select::make('Slots')
                    ->label('Slots')
                    ->options([
                        '2' => '2',
                        '4' => '4',
                    ])
                    ->required()
                    ->placeholder('Select the number of slots')
                    ->native(false),
                // TextInput::make('code')
                //     ->label('Code')
                //     ->required()
                //     ->unique()
                //     ->placeholder('Enter the room code')
                //     ->autofocus(),
                // TextInput::make('name')
                //     ->label('Name')
                //     ->required()
                //     ->placeholder('Enter the room name'),
                // TextInput::make('slots')
                //     ->label('Slots')
                //     ->type('number')
                //     ->required()
                //     ->placeholder('Enter the number of slots'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slots')
                    ->label('Slots')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PeriodsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
