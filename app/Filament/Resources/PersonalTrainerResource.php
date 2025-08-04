<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalTrainerResource\Pages;
use App\Filament\Resources\PersonalTrainerResource\RelationManagers\ClientsRelationManagerRelationManager;
use App\Models\PersonalTrainer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PersonalTrainerResource extends Resource
{
    protected static ?string $model = PersonalTrainer::class;
    protected static ?string $navigationGroup = 'Menu Transaksi';

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    public static function getNavigationLabel(): string
    {
        return 'Personal Trainer';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('price_per_visit')
                    ->required()
                    ->numeric()

                    ->prefix('Rp')
                    ->live(500),
                Forms\Components\TextInput::make('price_per_month')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->live(500),
                Forms\Components\FileUpload::make('photo')
                    ->label('Foto Profil')
                    ->image()
                    ->disk('public')
                    ->maxSize(1024)
                    ->imageCropAspectRatio('1:1')
                    ->directory('images/employee/profil')->columns(2)
                    ->columnSpan([
                        'lg' => 2,
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('price_per_visit')->searchable(),
                Tables\Columns\TextColumn::make('price_per_month')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(fn (PersonalTrainer $record) => PersonalTrainerResource::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ClientsRelationManagerRelationManager::class,
        ];
    }
    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
        ->schema([

            ImageEntry::make('photo')
                ->label('Foto Profil')
                ->circular()
                ->columnSpanFull(),

            TextEntry::make('name')
                ->label('Nama PT')
                ->color('primary')
                ->weight('bold'),

            TextEntry::make('phone')
                ->label('Nomor Telepon'),

            TextEntry::make('price_per_visit')
                ->label('Harga per Kunjungan')
                ->money('IDR'),

            TextEntry::make('price_per_month')
                ->label('Harga per Bulan')
                ->money('IDR'),

            TextEntry::make('created_at')
                ->label('Tanggal Dibuat')
                ->dateTime('d M Y H:i'),

            TextEntry::make('updated_at')
                ->label('Terakhir Diperbarui')
                ->dateTime('d M Y H:i'),
        ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalTrainers::route('/'),
            'create' => Pages\CreatePersonalTrainer::route('/create'),
            'edit' => Pages\EditPersonalTrainer::route('/{record}/edit'),
            'view' => Pages\ViewPersonalTrainer::route('/{record}'),
        ];
    }
}
