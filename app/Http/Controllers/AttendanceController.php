<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function scan($code)
    {
        $employee = Employee::where('qr_code', $code)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai tidak ditemukan',
            ], 404);
        }

        $today = now()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            // Belum absen → check-in
            Attendance::create([
                'employee_id' => $employee->id,
                'check_in' => now(),
                'date' => $today,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Check-in berhasil',
                'employee' => $employee->name,
            ]);
        }

        if ($attendance->check_out === null) {
            // Sudah check-in, belum check-out
            $attendance->update([
                'check_out' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Check-out berhasil',
                'employee' => $employee->name,
            ]);
        }

        // Sudah check-in dan check-out
        return response()->json([
            'status' => false,
            'message' => 'Pegawai sudah absen dan checkout hari ini',
            'employee' => $employee->name,
        ]);
    }
}
