<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    public function show($hash)
    {
        // Ambil member berdasarkan QR code (hash)
        $member = Member::where('qr_code', $hash)->firstOrFail();

        $startDate = \Carbon\Carbon::parse($member->joined);
        $endDate = \Carbon\Carbon::parse($member->expired);

        // Ambil semua attendance sesuai rentang tanggal aktif member
        $attendances = MemberAttendance::where('member_id', $member->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // Kelompokkan berdasarkan tanggal
        //         $latihanSelamaAktif = [];

        // $grouped = $attendances->groupBy(fn ($a) => $a->date->toDateString());

        // for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        //     $formatted = $date->toDateString();
        //     $latihanSelamaAktif[$formatted] = isset($grouped[$formatted]) ? count($grouped[$formatted]) : 0;
        // }

        // $latihanSelamaAktif = [];

        // foreach ($attendances as $attendance) {
        //     $formatted = $attendance->date->toDateString();
        //     if (!isset($latihanSelamaAktif[$formatted])) {
        //         $latihanSelamaAktif[$formatted] = 0;
        //     }
        //     $latihanSelamaAktif[$formatted]++;
        // }
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $latihanSelamaAktif[$date->toDateString()] = 0;
        }
        foreach ($attendances as $attendance) {
            $formatted = Carbon::parse($attendance->date)->toDateString();
            if (isset($latihanSelamaAktif[$formatted])) {
                $latihanSelamaAktif[$formatted]++;
            }
        }
         // Siapkan data chart
         $chartLabels = array_keys($latihanSelamaAktif);
         $chartData = array_values($latihanSelamaAktif);

        return view('member.dashboard', [
            'member' => $member,
            'latihanSelamaAktif' => $latihanSelamaAktif,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }
}
