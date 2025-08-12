<?php

namespace App\Http\Controllers;

use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\MemberAttendance;
use Illuminate\Http\Request;

class MemberAttendanceController extends Controller
{
    public function scanMember($code)
    {
        $member = Member::where('qr_code', $code)->first();

        if (! $member) {
            return response()->json([
                'status' => false,
                'message' => 'Member tidak ditemukan',
            ], 404);
        }

        if ($member->expired && now()->greaterThan($member->expired)) {
            return response()->json([
                'status' => false,
                'message' => 'Keanggotaan telah kedaluwarsa',
            ], 403);
        }

        MemberAttendance::create([
            'member_id' => $member->id,
            'date' => now()->toDateString(),
            'check_in_time' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Absen berhasil',
            'member' => $member->name,
        ]);
    }
}
