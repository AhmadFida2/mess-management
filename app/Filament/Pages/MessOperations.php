<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestAttendances;
use App\Filament\Widgets\MarkAttendance;
use App\Models\Attendance;
use App\Models\Bill;
use App\Models\BreakfastItems;
use App\Models\Member;
use Faker\Provider\Text;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;

class MessOperations extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mess-operations';

    public function mount()
    {
        $check = auth()->user()->is_admin || auth()->user()->is_staff;
       abort_unless($check,401);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_admin || auth()->user()->is_staff;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MarkAttendance::class
        ];


    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Pages\Actions\Action::make('print')->label('Print Attendance Sheet')
                ->url(fn (): string => route('attendance-print'))
                ->openUrlInNewTab()
                ->color('success')
                ->icon('heroicon-o-printer')
                ->tooltip('Print this Bill to A4 Format'),
            \Filament\Pages\Actions\Action::make('breafast_attendance')->label('Breakfast Attendance')
            ->color('primary')
            ->icon('lineawesome-bread-slice-solid')
            ->tooltip('Mark Breakfast Attendance')
                ->action(function (array $data): void {
                    $mid = $data['member_id'];
                    $data = $data['breakfast_units'];
                        $totalunits = 0;
                   foreach ($data as $index=>$value)
                            $totalunits += $value['units'];
                    $att = new Attendance([
                        'date' => now()->format('Y-m-d'),
                        'meal' => 'Breakfast',
                        'units' => $totalunits,
                        'member_id' => $mid,
                    ]);
                        $att->save();
                    Notification::make()
                        ->title('Attendance Saved!')
                        ->success()
                        ->send();
                })
                ->form([
                    Select::make('member_id')->label('Select Member')->required()->options(function(){
                            return Member::query()->pluck('name','id');
                    }),
                   Repeater::make('breakfast_units')
                       ->schema([
                           Select::make('name')->label('Item Name')
                           ->options(function() {
                               $items = BreakfastItems::query()->pluck('name','id');
                               return $items;
                           })
                               ->reactive()
                           ->afterStateUpdated(function ($state, callable $get,$set)
                           {
                               $units = BreakfastItems::find($state)->units ?? 0;
                               $set('units', $units);
                           })->dehydrated(false),
                           TextInput::make('quantity')->numeric()
                               ->minValue(0)
                               ->default(0)->dehydrated(false),
                           Hidden::make('units')->default(0)
                               ->dehydrateStateUsing(fn($state, callable $get) => $state = $get('quantity')*$state),
                       ])
                       ->createItemButtonLabel('Add Item')
                       ->columns(2)
                ])
        ];
    }
}
