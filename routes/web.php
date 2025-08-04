<?php

use App\Http\Controllers\AttendanceController;
use App\Models\Employee;
use Illuminate\Support\Facades\Route;
Route::get('/scan/{code}', [AttendanceController::class, 'scan']);
Route::get('/qr/{uuid}', function ($qr_code) {
    $employee = Employee::where('qr_code', $qr_code)->firstOrFail();
    return response()->view('qr.show', compact('employee'));
})->name('qr.view');
