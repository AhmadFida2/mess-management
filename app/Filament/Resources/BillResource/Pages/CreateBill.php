<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Events\BillGenerated;
use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\Member;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function afterCreate()
    {
        if($this->record->status>0)
        {
            $payment_amount = $this->data['payment_amount'];
            $bid = $this->record->id;
            $mid = $this->data['member_id'];
            $member = Member::find($mid);
            $member->account_balance += $this->record->amount;
            $member->account_balance -= $payment_amount;
            $member->save();
            $pmid = $this->data['payment_method'];
            $pmt = new Payment([
                'amount' => $payment_amount,
                'member_id' => $mid,
                'bill_id' =>   $bid,
                'payment_method_id' =>  $pmid
            ]);
            $pmt->save();
        }
        else
        {
            $mid = $this->data['member_id'];
            $member = Member::find($mid);
            $member->account_balance += $this->record->amount;
            $member->save();
        }
        BillGenerated::dispatch($this->record);
        $user = $member->user;
        if(isset($user))
        {
            Notification::make()
                ->title('New Bill Generated')
                ->body('Bill Month: '. Carbon::parse($this->record->month)->format('F Y') . ' Amount: ' . $this->record->amount)
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(route('bill-print', ['id' => $this->record->id]), shouldOpenInNewTab: true)
                ])
                ->sendToDatabase(auth()->user());
        }
    }


}

