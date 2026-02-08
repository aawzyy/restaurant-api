<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/MenuResource.php

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\FileUpload::make('image_path')
                        ->label('Foto Menu')
                        ->image()
                        ->disk('s3') // <--- WAJIB: Supaya masuk MinIO
                        ->directory('menu-images') // Masuk folder menu-images di bucket
                        ->visibility('public')
                        ->required(),

                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Kategori')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([ // Fitur Quick Create Kategori (Bonus UX)
                            Forms\Components\TextInput::make('name')->required(),
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Menu')
                        ->required(),

                    Forms\Components\TextInput::make('price')
                        ->label('Harga')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\Toggle::make('is_available')
                        ->label('Tersedia?')
                        ->default(true),
                ])->columns(1), // Form memanjang ke bawah
            ]);
    }

    // Bagian TABLE
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan Gambar Kecil di Tabel
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto')
                    ->disk('s3')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge() // Biar tampilannya kotak berwarna
                    ->color('info'),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR') // Format otomatis Rp 25.000
                    ->sortable(),

                // Saklar On/Off langsung di tabel
                Tables\Columns\ToggleColumn::make('is_available')
                    ->label('Stok'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ]);
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
