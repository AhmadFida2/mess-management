<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\UnitCost;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\Rules\Unique;
use function App\Filament\Resources\getMonthlyAttendance;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Bulk Attendance')
                ->action(function (array $data): void {
                    $count = count($data['member_id']);
                    foreach ($data['member_id'] as $m=>$id)
                    {
                        $date = Carbon::parse($data['date'])->format('Y-m-d');
                        $units = $data['units'];
                        $attendance = new Attendance();
                        $attendance->date = $date;
                        $attendance->units = $units;
                        $attendance->meal = $data['meal'];
                        $attendance->member_id = $id;
                        $attendance->save();
                    }
                    {
                        Notification::make()
                            ->title($count. ' Attendences Created!')
                            ->success()
                            ->send();
                    }

                })
                ->form([
                    DatePicker::make('date')->default(now())->displayFormat('d-M-Y')->required(),
                    Select::make('meal')->required() ->options([
                        'Breakfast' => 'Breakfast',
                        'Lunch' => "Lunch",
                        'Dinner' => 'Dinner'
                    ]),
                    TextInput::make('units')->required()->numeric(),
                    MultiSelect::make('member_id')
                        ->label('Members')
                        ->options(Member::query()->pluck('name', 'id'))
                        ->required()->searchable()->disablePlaceholderSelection()->default(true),

                ])->requiresConfirmation()->color('success')
        ];
    }
}
