<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Models\Bill;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'amount';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->date('d M y'),
                Tables\Columns\TextColumn::make('amount'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
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
             //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
