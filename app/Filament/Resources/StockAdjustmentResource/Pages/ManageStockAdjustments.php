<?php

namespace App\Filament\Resources\StockAdjustmentResource\Pages;

use App\Filament\Resources\StockAdjustmentResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageStockAdjustments extends ManageRecords
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function handleRecordCreation(array $data): Model
    {
        // Ambil produk
        $product = Product::findOrFail($data['product_id']);

        // Hitung stok baru
        if ($data['adjustment_type'] === 'increase') {
            $product->stock_quantity += $data['quantity_adjusted'];
        } else {
            // Validasi stok tidak boleh minus
            if ($product->stock_quantity < $data['quantity_adjusted']) {
                throw new \Exception("Stok produk {$product->name} tidak mencukupi untuk dikurangi!");
            }
            $product->stock_quantity -= $data['quantity_adjusted'];
        }

        // Simpan stok baru
        $product->save();

        // Simpan data penyesuaian stok
        $data['user_id'] = auth()->id();

        return static::getModel()::create($data);
    }
}
