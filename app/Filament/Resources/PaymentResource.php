<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Bill;
use App\Models\Member;
use App\Models\Payment;
use Closure;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Mess Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')->label('Payment Amount')->numeric()
                    ->reactive()->afterStateUpdated(function ($state, callable $set, $get) {
                        if (null !== $get('bill_id')) {
                            $billAmount = Bill::find($get('bill_id'))->amount;
                            $payments = DB::table('payments')->where('bill_id', '=', $get('bill_id'))->sum('amount');
                            $check = $billAmount - $payments;
                            if (($check - $state) <= 0) {
                                $set('bill.status', 2);
                            } elseif (($check - $state) > 0) {
                                $set('bill.status', 1);
                            }
                        }
                        $bal = Member::find($get('member_id'))->account_balance;
                        $bal -= floatval($get('amount'));
                        $set('member.account_balance', $bal);
                    }),
                Select::make('member_id')
                    ->options(Member::all()->pluck('name', 'id')->toArray())->label('Member Name')->reactive(),
                Select::make('bill_id')
                    ->options(function (callable $get) {
                        $bills = DB::Table('bills')->where('member_id', '=', $get('member_id'))
                            ->where('status', '<', 2)
                            ->pluck('bill_details', 'id');
                        return $bills;
                    })->label('Select Bill')->reactive()->afterStateUpdated(function ($state, callable $set, $get) {
                        $billAmount = Bill::find($state)->amount;
                        $payments = DB::table('payments')->where('bill_id', '=', $state)->sum('amount');
                        $diff = $billAmount - $payments;
                        $set('amount', floatval(($diff)));
                        $bal = Member::find($get('member_id'))->account_balance;
                        $bal -= floatval($get('amount'));
                        $set('member.account_balance', $bal);


                    }),
                Select::make('payment_method_id')->relationship('payment_method', 'name')->required(),
                Fieldset::make('Bill Payment Status')->relationship('bill')
                    ->schema([
                        TextInput::make('status')->label('Status')->disabled()->default(2),
                    ]),
                Fieldset::make('Member Account Details')->relationship('member')
                    ->schema([
                        TextInput::make('account_balance')->label('Account Balance')->disabled(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount'),
                TextColumn::make('member.name')->label('Member'),
                TextColumn::make('bill.bill_details')->label('Bill Details')
                    ->formatStateUsing(fn($state) => $state ?? "On Account"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Model $record) {
                        if ($record->amount > 0) {
                            $member = Member::find($record->member_id);
                            $payment_amount = $record->amount;
                            $member->account_balance += $payment_amount;
                            $member->save();
                            $bill = Bill::find($record->bill_id);
                            if (isset($bill)) {
                                if ($bill->amount - $payment_amount <= 0) {
                                    $bill->status = 0;
                                } elseif ($bill->amount - $payment_amount > 0) {
                                    $bill->status = 1;
                                }

                                $bill->save();
                            }
                        }
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
