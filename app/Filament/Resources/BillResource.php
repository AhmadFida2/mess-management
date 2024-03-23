<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers;
use App\Filament\Resources\BillResource\Widgets\BlogPostsChart;
use App\Filament\Resources\BillResource\Widgets\StatsOverview;
use App\Models\Bill;
use App\Models\DailyAttendance;
use App\Models\PaymentMethod;
use App\Models\UnitCost;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;
function getAutoAttendances($member, string $int): float|int
{
    $start = Carbon::createFromDate(now()->year,$int,1)->format('Y-m-d');
    $end = Carbon::createFromDate(now()->year,$int,1)->endOfMonth()->format('Y-m-d');

    $atts = DailyAttendance::where('member_id','=',$member)->whereBetween('date',[$start,$end])->select(['date','is_lunch','is_dinner'])
        ->with('units')->get();
    $sum = 0;
    foreach($atts as $att)
    {
        if(isset($att->units))
        {
            $lunch_cost = $att->is_lunch * $att->units->lunch??0;
            $dinner_cost = $att->is_dinner * $att->units->dinner??0;
            $sum += $lunch_cost + $dinner_cost;
        }
        else
        {
            $sum+=0;
        }
    }
    return $sum;
}

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'fas-file-invoice-dollar';
    protected static ?string $navigationGroup = 'Mess Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
             Flatpickr::make('month')->required()->monthSelect()->unique(modifyRuleUsing:   function(Unique $rule,callable $get) {
                 return $rule->Where('member_id',$get('member_id'));})->default(now()->startOfMonth()->format('Y-m-d'))
                 ->altFormat('F Y')
                 ->altInput()
                 ->dateFormat('Y-m-d')
                 ->reactive()
                ->afterStateUpdated(function ($state,callable $get,$set) {
                    $date = Carbon::parse($state)->format('Y-m-d');
                    $cost = UnitCost::where('month', $date)->first()->cost ?? 0;
                    $set('unit_cost',$cost);
                    if($get('member_id'))
                    {
                        $month = Carbon::Parse($get('month'))->format('m');
                        $autosum = getAutoAttendances($get('member_id'),$month);
                        $attendances = getMonthlyAttendance($get('member_id'),$month);
                        $sum = $attendances->sum('units');
                        $sum = max($sum, 0);
                        $set('units',($sum+$autosum));
                        $set('amount',($sum+$autosum)*$get('unit_cost'));
                    }
                })->afterStateHydrated(function ($state,callable $get,$set) {
                    $date = Carbon::parse($state)->format('Y-m-d');
                    $cost = UnitCost::where('month', $date)->first()->cost ?? 0;
                    $set('unit_cost',$cost);
                    if($get('member_id'))
                    {
                        $month = Carbon::Parse($get('month'))->format('m');
                        $autosum = getAutoAttendances($get('member_id'),$month);
                        $attendances = getMonthlyAttendance($get('member_id'),$month);
                        $sum = $attendances->sum('units');
                        $sum = max($sum, 0);
                        $set('units',($sum+$autosum));
                        $set('amount',($sum+$autosum)*$get('unit_cost'));
                    }
                }),
                TextInput::make('units')->label('Units Consumed')->numeric()
                ->disabled()->dehydrated(),
                TextInput::make('unit_cost')->label('Unit Cost')->numeric()
                    ->disabled()->dehydrated(false)->default(function ($get){
                        $date = Carbon::parse($get('month'))->startOfMonth();
                        return UnitCost::where('month','=',$date)->first()->cost??"0";
                    }),
                TextInput::make('amount')->label('Bill Amount')->numeric()->disabled()->dehydrated(),
                Select::make('status')
                    ->options([
                        0 => 'Unpaid',
                        1 => 'Partial Paid',
                        2 => 'Paid',
                    ])->reactive()->default( 0)
                ->afterStateUpdated(function ($get,$set,$state){
                    if($state==2)
                        $set('payment_amount',$get('amount'));
                }),
                Select::make('member_id')
                    ->relationship('member', 'name')
                ->reactive()->afterStateUpdated(function ($state,callable $get,$set){
                    $month = Carbon::Parse($get('month'))->format('m');
                    $autosum = getAutoAttendances($state,$month);
                    $attendances = getMonthlyAttendance($state,$month);
                    $sum = $attendances->sum('units');
                    $sum = max($sum, 0);
                    $set('units',($sum+$autosum));
                    $set('amount',($sum+$autosum)*$get('unit_cost'));
              //      self::updateAccountBalance($state, $get('amount'));
                    }),
                TextInput::make('payment_amount')->numeric()->default(0)
                    ->visible(fn($get)=> $get('status')>0)->required()->reactive()
                ->disabled(fn($get)=> $get('status')===2),
                Select::make('payment_method')->label('Payment Method')
                    ->options(fn() => PaymentMethod::query()->pluck('name','id'))
                    ->required()->visible(fn($get)=> $get('status')>0),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('member.name')->label('Member'),
                TextColumn::make('month')->label('Bill Month')->date('F Y'),
                TextColumn::make('units')->label('Units Consumed'),
                TextColumn::make('amount')->label('Bill Amount'),
                TextColumn::make('Paid Amount')
                    ->getStateUsing(function($record) {
                        return DB::Table('payments')->where('bill_id','=',$record->id)->sum('amount');
                    }),
                TextColumn::make('status')->label('Status')->getStateUsing(function ($record) {
                    $stats = ['Un-Paid','Partial Paid','Paid'];
                    return $stats[$record->status];
                })
                    ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('print')->label('Print')
                    ->url(fn (Bill $record): string => route('bill-print', $record->id))
                    ->openUrlInNewTab()
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->tooltip('Print this Bill to A4 Format')
            ])
            ->bulkActions([
              //  Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
           RelationManagers\PaymentsRelationManager::class,
            RelationManagers\MemberRelationManager::class,

        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
            BlogPostsChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}
