<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

function getMonthlyAttendance($member, int $int): \Illuminate\Support\Collection
{
    $start = Carbon::createFromDate(now()->year,$int,1)->format('Y-m-d');
    $end = Carbon::createFromDate(now()->year,$int,1)->endOfMonth()->format('Y-m-d');
    return  DB::table('attendances')->where('member_id','=',$member)->whereBetween('date',[$start,$end])->get();
}

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-check';

    protected static ?string $navigationGroup = 'Mess Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')->default(now())->displayFormat('d-M-Y')->required(),
                Select::make('meal')->required() ->options([
                   'Breakfast' => 'Breakfast',
                   'Lunch' => "Lunch",
                   'Dinner' => 'Dinner'
                ]),
                TextInput::make('units')->required()->numeric(),
                Select::make('member_id')->relationship('member','name')->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name'),
                TextColumn::make('date')->date('d M Y'),
                TextColumn::make('meal'),
                TextColumn::make('units'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('member_id')->attribute('member.name')->label('Member')
            ])
            ->actions([
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

}
