<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;
    public static function boot()
    {
        parent::boot();

        static::created(function ($adjustment) {
            $product = $adjustment->product;

            if ($adjustment->adjustment_type === 'increase') {
                // Tambah stok
                $product->increment('stock_quantity', $adjustment->quantity_adjusted);
            } else {
                // Kurangi stok tapi pastikan tidak minus
                if ($product->stock_quantity < $adjustment->quantity_adjusted) {
                    Notification::make()
                        ->title('Stok Tidak Cukup')
                        ->body("Stok untuk {$product->name} tidak mencukupi!")
                        ->danger()
                        ->send();

                    // Batalkan perubahan
                    $adjustment->delete();
                    return;
                }

                $product->decrement('stock_quantity', $adjustment->quantity_adjusted);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
