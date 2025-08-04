<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    protected static function booted()
    {
        static::creating(function ($employee) {
            $employee->qr_code = Str::uuid(); // atau bisa pakai hash unik
        });
    }
}
