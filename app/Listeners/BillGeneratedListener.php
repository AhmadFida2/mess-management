<?php

namespace App\Listeners;

use App\Events\BillGenerated;
use App\Mail\BillDetail;
use Carbon\Carbon;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class BillGeneratedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BillGenerated $billGenerated)
    {
//        $url = 'https://seeds-erp.app/bill/print/'. $billGenerated->bill->id;
//        $name = $billGenerated->bill->member->name;
//        $email = $billGenerated->bill->member->email;
//        $billAmount = $billGenerated->bill->amount;
//        $units = $billGenerated->bill->units;
//        $dueDate = Carbon::parse($billGenerated->bill->month)->addDays(6)->format('d-M-Y');
//        Mail::to($email)
//            ->send(new BillDetail($name,$billAmount,$units,$dueDate,$url));
    }
}

