<?php

namespace App\Filament\Resources\BillResource\RelationManagers;

use App\Models\Bill;
use App\Models\Member;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'date';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')->label('Payment Amount')->numeric()
                    ->reactive()->afterStateUpdated(function ($state,callable $set,$get,$livewire){
                        $billAmount = Bill::find($livewire->ownerRecord->id)->amount;
                        $payments = DB::table('payments')->where('bill_id','=',$livewire->ownerRecord->id)->sum('amount');
                        $check = $billAmount - $payments;
                        if(($check-$state)<=0)
                        {
                            $set('bill.status',2);
                        }
                        elseif(($check-$state)>0)
                        {
                            $set('bill.status',1);
                        }
                    }),
                Select::make('member_id')
                    ->options(function ($livewire)
                    {
                        return [
                            $livewire->ownerRecord->member_id => $livewire->ownerRecord->member->name
                        ];
                    })->default(fn ($livewire)=> $livewire->ownerRecord->member_id)
                    ->label('Member Name')
                    ->disablePlaceholderSelection(),
                Fieldset::make('Bill Payment Status')->relationship('bill')
                    ->schema([
                        TextInput::make('status')->label('Status')->disabled()->default(2),
                    ])->hidden(true),
                Select::make('payment_method_id')->relationship('payment_method','name')->required(),

            ]);

    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->disabled(function($livewire)
                {
                   $status = false;
                   if($livewire->ownerRecord->status==2) $status = true;
                   return $status;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Model $record)
                    {
                        if($record->amount>0)
                        {
                            $member = Member::find($record->member_id);
                            $payment_amount = $record->amount;
                            $member->account_balance += $payment_amount;
                            $member->save();
                            $bill  = Bill::find($record->bill_id);
                            if(isset($bill))
                            {
                            if($bill->amount - $payment_amount <= 0)
                            {
                                $bill->status =0;
                            }
                            elseif ($bill->amount - $payment_amount > 0)
                            {
                                $bill->status = 1;
                            }
                           
                            $bill->save();
                            }
                        }
                    }),
            ])
            ->bulkActions([
            //    Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
