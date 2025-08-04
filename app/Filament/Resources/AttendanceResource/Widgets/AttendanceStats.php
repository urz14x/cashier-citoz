<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use Filament\Forms\Components\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->toDateString();
        $todayAttendance = Attendance::whereDate('date', $today)->count();
        return [
            Stat::make('Total pegawai', Employee::count())->color('success')->description('Dari keseluruhan')
            ->descriptionIcon('heroicon-o-users'),
            Stat::make('Pegawai yang telah absen', $todayAttendance),
        ];
    }
}
