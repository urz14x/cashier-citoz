<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAttendance extends Model
{
    /** @use HasFactory<\Database\Factories\MemberAttendanceFactory> */
    use HasFactory;
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    protected $casts = [
        'check_in_time' => 'datetime',
        'date' => 'date',
    ];
}
