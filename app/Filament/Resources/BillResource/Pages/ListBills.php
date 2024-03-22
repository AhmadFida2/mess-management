<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Events\BillGenerated;
use App\Filament\Resources\BillResource;
use App\Filament\Resources\BillResource\Widgets\BlogPostsChart;
use App\Models\Attendance;
use App\Models\Bill;
use App\Models\Member;
use App\Models\UnitCost;
use Carbon\Carbon;
use Filament\Forms\Components\MultiSelect;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\Rules\Unique;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use function App\Filament\Resources\getMonthlyAttendance;

class ListBills extends ListRecords
{
    protected static string $resource = BillResource::class;

    function getMonthlyAttendance($member, int $int)
    {
        $start = Carbon::createFromDate(now()->year,$int,1)->format('Y-m-d');
        $end = Carbon::createFromDate(now()->year,$int,1)->endOfMonth()->format('Y-m-d');
        return  Attendance::where('member_id','=',$member)->whereBetween('date',[$start,$end])->pluck('units','id')->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Multiple Bills')
                ->action(function (array $data): void {
                    foreach ($data['member_id'] as $member=>$id)
                    {
                        $date = Carbon::parse($data['month'])->format('Y-m-d');
                        $unit_cost = UnitCost::where('month','=',$date)->first();
                        if(is_null($unit_cost)){
                            Notification::make()
                                ->title('No Unit Cost Defined!')
                                ->danger()
                                ->send();
                            return;
                        }
                        $unit_cost = $unit_cost->cost;
                        $attendances = getMonthlyAttendance($id,Carbon::parse($data['month'])->format('m'));
                        $sum = $attendances->sum('units');
                        $sum = max($sum, 0);
                        if($sum==0)  continue;
                        $bill = new Bill();
                        $bill->month = $date;
                        $bill->units = $sum;
                        $bill->amount = $bill->units * $unit_cost;
                        $bill->status = 0;
                        $bill->member_id = $id;
                        $bill->save();
                        $member = Member::find($id);
                        $member->account_balance += $bill->amount;
                        $member->save();
                        BillGenerated::dispatch($bill);
                        $user = $member->user;
                        if(isset($user))
                        {
                            Notification::make()
                                ->title('New Bill Generated')
                                ->body('Bill Month: '. Carbon::parse($bill->month)->format('F Y') . ' Amount: ' . $bill->amount)
                                ->actions([
                                    Action::make('view')
                                        ->button()
                                        ->url(route('bill-print', ['id' => $bill->id]), shouldOpenInNewTab: true)
                                ])
                                ->sendToDatabase(auth()->user());
                        }

                    }
                    {
                        Notification::make()
                            ->title('All Bills Generated!')
                            ->success()
                            ->send();
                    }

                })
                ->form([
                    Flatpickr::make('month')->required()->monthSelect()->unique(function(Unique $rule,callable $get) {
                        return $rule->Where('member_id',$get('member_id'));})->default(now())->validationAttribute('Some Members already have bills for the given month. Kindly remove their names.'),
                    MultiSelect::make('member_id')
                        ->label('Members')
                        ->options(Member::query()->pluck('name','id'))
                        ->required()->searchable()->disablePlaceholderSelection(),

                ])->requiresConfirmation()->color('success'),
            Actions\Action::make('All Bills')
            ->action(function(array $data){
                $members = Member::query()->pluck('id','id');
                foreach ($members as $member=>$id)
                {

                    $date = Carbon::parse($data['month'])->format('Y-m-d');
                    $bill = Bill::where('month',$date)->where('member_id',$id)->first();
                    if(isset($bill)) continue;
                    $unit_cost = UnitCost::where('month','=',$date)->first();
                    if(is_null($unit_cost)){
                        Notification::make()
                            ->title('No Unit Cost Defined!')
                            ->danger()
                            ->send();
                        return;
                    }
                    $unit_cost = $unit_cost->cost;
                    $attendances = getMonthlyAttendance($id,Carbon::parse($data['month'])->format('m'));
                    $sum = $attendances->sum('units');
                    $sum = max($sum, 0);
                    if($sum==0)  continue;
                    $bill = new Bill();
                    $bill->month = $date;
                    $bill->units = $sum;
                    $bill->amount = $bill->units * $unit_cost;
                    $bill->status = 0;
                    $bill->member_id = $id;
                    $bill->save();
                    $member = Member::find($id);
                    $member->account_balance += $bill->amount;
                    $member->save();
                    //BillGenerated::dispatch($bill);
                    $user = $member->user;
                    if(isset($user))
                    {
                        Notification::make()
                            ->title('New Bill Generated')
                            ->body('Bill Month: '. Carbon::parse($bill->month)->format('F Y') . ' Amount: ' . $bill->amount)
                            ->actions([
                                Action::make('view')
                                    ->button()
                                    ->url(route('bill-print', ['id' => $bill->id]), shouldOpenInNewTab: true)
                            ])
                            ->sendToDatabase(auth()->user());
                    }
                }
                {
                    Notification::make()
                        ->title('All Bills Generated!')
                        ->success()
                        ->send();
                }
            }) ->form([
                    Flatpickr::make('month')->required()->monthSelect()->default(now())
                        ->altFormat('F Y')
                        ->altInput(),
                ])->color('danger')->requiresConfirmation()

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BillResource\Widgets\StatsOverview::class,
            BlogPostsChart::class,
        ];
    }
}

