<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MemberStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function personalTrainers()
    {
        return $this->belongsToMany(PersonalTrainer::class, 'member_personal_trainer');
    }

    /**
     * Accessor untuk status member berdasarkan tanggal.
     */

    public function scopeExpired($query)
    {
        return $query->where('expired', '<', now());
    }
    public function getHasTransactionAttribute(): bool
    {
        return $this->orders()->exists();
    }
    public function getStatusAttribute(): MemberStatus
    {
        if (!$this->expired || !$this->joined) {
            return MemberStatus::EXPIRED;
        }

        $now = now();
        $expired = \Carbon\Carbon::parse($this->expired);

        if ($now->gt($expired)) {
            return MemberStatus::EXPIRED;
        }

        if ($this->updated_at && $this->created_at && $this->updated_at->gt($this->created_at)) {
            return MemberStatus::EXTEND;
        }

        return MemberStatus::ACTIVE;
    }


    /**
     * Opsional: Warna badge berdasarkan status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            MemberStatus::ACTIVE => 'success',
            MemberStatus::EXPIRED => 'danger',
            MemberStatus::EXTEND => 'info',
            default => 'secondary',
        };
    }

    protected $casts = [
        'gender' => Gender::class,
    ];
}
