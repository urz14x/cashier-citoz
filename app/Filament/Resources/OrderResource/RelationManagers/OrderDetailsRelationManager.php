<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\OrderDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetails';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix(fn(OrderDetail $record) => $record->quantity . ' x ')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->alignEnd(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire) {
                        $livewire->getOwnerRecord()->calculateTotal();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire) {
                        $livewire->getOwnerRecord()->calculateTotal();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->getOwnerRecord()->calculateTotal();
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
