<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\RelationManagers\StockAdjustmentsRelationManager;
use App\Filament\Resources\StockAdjustmentResource\Pages;
use App\Models\StockAdjustment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static ?string $navigationGroup = 'Stock';
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hiddenOn(StockAdjustmentsRelationManager::class),
                Forms\Components\Select::make('adjustment_type')
                    ->label('Tipe Penyesuaian')
                    ->options([
                        'increase' => 'Tambah Stok',
                        'decrease' => 'Kurangi Stok',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity_adjusted')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->maxLength(65535)
                    ->default('Restock.')
                    ->placeholder('Write a reason for the stock adjustment')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->sortable()
                    ->hiddenOn(StockAdjustmentsRelationManager::class),
                Tables\Columns\TextColumn::make('quantity_adjusted')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adjustment_type')
                    ->label('Tipe')
                    ->badge()
                    ->colors([
                        'success' => 'increase',
                        'danger' => 'decrease',
                    ]),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->default('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->hiddenOn(StockAdjustmentsRelationManager::class),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStockAdjustments::route('/'),
        ];
    }
}
