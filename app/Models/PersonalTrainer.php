<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalTrainer extends Model
{
    /** @use HasFactory<\Database\Factories\PersonalTrainerFactory> */
    use HasFactory;
    public function members()
    {
        return $this->belongsToMany(Member::class)->withTimestamps();
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_personal_trainer')->withPivot('pt_type')
        ->withTimestamps();
    }
}
