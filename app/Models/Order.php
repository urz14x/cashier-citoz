<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
    public function personalTrainers()
    {
        return $this->belongsToMany(PersonalTrainer::class)
            ->withPivot('pt_type')
            ->withTimestamps();
    }

    public function calculateTotal(): void
    {
        $this->loadMissing(['orderDetails.product', 'personalTrainers']);

        $productTotal = $this->orderDetails->sum(fn($d) => $d->price * $d->quantity);

        $trainerTotal = $this->personalTrainers->sum(function ($pt) {
            return $pt->pivot->pt_type === 'per_bulan'
                ? $pt->price_per_month
                : $pt->price_per_visit;
        });

        $discount = $this->discount ?? 0;

        $this->base_total = $productTotal;
        $this->total = max(0, $productTotal + $trainerTotal - $discount);
        $this->save();
    }
    protected $cast = [
        'status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'base_total' => 'integer',
        'total' => 'integer',
        'discount' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->user_id = auth()->id();
            $order->total = 0;
        });

        static::saving(function ($order) {
            if ($order->isDirty('total')) {
                $order->loadMissing('orderDetails.product');

                $profitCalculation = $order->orderDetails->reduce(function ($carry, $detail) {
                    $productProfit = ($detail->price - $detail->product->cost_price) * $detail->quantity;
                    return $carry + $productProfit;
                }, 0);

                $order->attributes['profit'] = $profitCalculation;
            }
        });
    }
    public function getRouteKeyName(): string
    {
        return 'order_number';
    }
    public function markAsComplete(): void
    {
        $this->status = \App\Enums\OrderStatus::COMPLETED;
        $this->save();
    }
}
