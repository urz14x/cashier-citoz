<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberAttendanceResource\Pages;
use App\Filament\Resources\MemberAttendanceResource\RelationManagers;
use App\Models\MemberAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberAttendanceResource extends Resource
{
    protected static ?string $model = MemberAttendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Absensi Member';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'name')
                    ->required(),

                Forms\Components\DateTimePicker::make('check_in_time')
                    ->required()
                    ->label('Jam masuk'),
                Forms\Components\DatePicker::make('date')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('member.name')->label('Member'),
            Tables\Columns\TextColumn::make('date')->date()->sortable(),
            Tables\Columns\TextColumn::make('check_in_time')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(),
        ])->defaultSort('check_in_time', 'desc')
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Hanya Hari Ini')
                    ->query(fn($query) => $query->whereDate('date', now()->toDateString())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMemberAttendances::route('/'),
            'create' => Pages\CreateMemberAttendance::route('/create'),
            'edit' => Pages\EditMemberAttendance::route('/{record}/edit'),
        ];
    }
}
