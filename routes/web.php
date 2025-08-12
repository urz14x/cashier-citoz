<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MemberAttendanceController;
use App\Http\Controllers\MemberDashboardController;
use App\Models\Employee;
use App\Models\Member;
use Illuminate\Support\Facades\Route;


Route::get('/scan/member/{code}', [MemberAttendanceController::class, 'scanMember']);
// Route::get('/qr/member/{code}', function ($qr_code) {
//     $member = Member::where('qr_code', $qr_code)->firstOrFail();
//     return response()->view('qr.member', compact('member'));
// })->name('qr.view');
Route::get('/member/dashboard/{code}', [MemberDashboardController::class, 'show'])->name('member.dashboard');
//Routing absen pegawai
Route::get('/scan/{code}', [AttendanceController::class, 'scan']);
//Tampilan untuk absen pegawai web
Route::get('/qr/{uuid}', function ($qr_code) {
    $employee = Employee::where('qr_code', $qr_code)->firstOrFail();
    return response()->view('qr.show', compact('employee'));
})->name('qr.view');
