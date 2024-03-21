<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Bill;
use App\Models\Member;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function (Model $record)

                {
                    if($record->amount>0)

                    {
                        $member = Member::find($record->member_id);
                        $payment_amount = $record->amount;
                        $member->account_balance += $payment_amount;
                        $member->save();
                        $bill  = Bill::find($record->bill_id);
                        if($bill->amount - $payment_amount == 0)
                        {
                            $bill->status =0;
                        }
                        elseif ($bill->amount - $payment_amount > 0)
                        {
                            $bill->status = 1;
                        }
                        else
                        {
                            $bill->status = 0;
                        }
                        $bill->save();
                    }
                }),
        ];
    }


}
