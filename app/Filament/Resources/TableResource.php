<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Models\Table as TableModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Bagian FORM
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('table_number')
                        ->label('Nomor Meja')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('Contoh: A1, B2, 05'),

                    Forms\Components\Select::make('status')
                        ->options([
                            'available' => 'Available (Kosong)',
                            'occupied' => 'Occupied (Terisi)',
                        ])
                        ->default('available')
                        ->required(),
                ]),
            ]);
    }

    // Bagian TABLE
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('table_number')
                    ->label('Nomor Meja')
                    ->sortable()
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success', // Hijau
                        'occupied' => 'danger',   // Merah
                    }),
            ])
            ->defaultSort('table_number');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}
