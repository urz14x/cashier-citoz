<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Filament\Resources\AttendanceResource\Widgets\AttendanceStats;
use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationGroup = 'Menu Pegawai';
    protected static ?string $navigationIcon = 'heroicon-o-document-check';


    public static function getWidgets(): array
    {
        return [
            AttendanceStats::class,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Absen Pegawai'; // ← Nama menu yang muncul di sidebar
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Nama pegawai')
                    ->disabled(fn($livewire) => $livewire->getRecord() !== null)
                    ->relationship('employee', 'name')

                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Waktu')
                    ->default(Carbon::now())
                    ->readOnly(fn($record) => $record && $record->date !== null)
                    ->disabledOn('edit')
                    ->required(),
                Forms\Components\TimePicker::make('check_in')
                    ->label('Jam masuk')
                    ->readOnly(fn($record) => $record && $record->check_in !== null)
                    ->disabledOn('edit'),
                Forms\Components\TimePicker::make('check_out')
                    ->label('Jam keluar'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->numeric()
                    ->label('Nama pegawai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal Absen')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Jam masuk')

                    ->weight(FontWeight::Black),
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Jam keluar')
                    ->weight(FontWeight::Black),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
