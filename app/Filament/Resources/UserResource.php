<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->description('Kelola data dasar pengguna aplikasi.')
                    ->schema([
                        // 1. Nama
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Lengkap'),

                        // 2. Email
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // Cek unik kecuali punya diri sendiri

                        // 3. Password (Logic Canggih)
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Hash sebelum simpan
                            ->dehydrated(fn ($state) => filled($state)) // Jangan kirim ke DB kalau kosong
                            ->required(fn (string $context): bool => $context === 'create') // Wajib cuma pas bikin baru
                            ->revealable()
                            ->label('Password'),
                        
                        // 4. No HP (Opsional, kalau ada kolomnya di DB)
                        // Forms\Components\TextInput::make('phone_number')
                        //    ->tel()
                        //    ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Integrasi')
                    ->schema([
                        // Tampilkan Google ID (Read Only) biar admin tau ini user Google
                        Forms\Components\TextInput::make('google_id')
                            ->label('Google ID (Linked)')
                            ->disabled() // Admin gak boleh edit manual
                            ->placeholder('Tidak terhubung dengan Google'),
                            
                        // Verifikasi Email Manual
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Tanggal Verifikasi Email'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Nama
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Kolom Email
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->copyable(),

                // Indikator User Google (Pakai Icon)
                Tables\Columns\IconColumn::make('google_id')
                    ->label('Google Login?')
                    ->boolean() // Kalau ada isinya = Centang, Kalau null = Silang
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                // Tanggal Dibuat
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Terdaftar Sejak'),
            ])
            ->filters([
                // Filter User Google vs Biasa
                Tables\Filters\TernaryFilter::make('google_id')
                    ->label('Tipe User')
                    ->placeholder('Semua User')
                    ->trueLabel('User Google')
                    ->falseLabel('User Email Biasa')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('google_id'),
                        false: fn ($query) => $query->whereNull('google_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
