<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class UserUI extends Controller
{
   public function dashboard()
   {

       $id = auth()->user()->member->id;
       $bills = Bill::where('member_id',$id)->latest()->select('id','month','units','amount','status')->take(5)->get();
       $attendances = Attendance::where('member_id',$id)
           ->whereBetween('date',[now()->startOfMonth()->format('Y-m-d'),now()->endOfMonth()->format('Y-m-d')])
           ->latest()->get();
       $payments = Payment::where('member_id',$id)->latest()->take(5)->get();
        $member = auth()->user()->member;
        $status = ['Unpaid','Partial Paid','Paid'];
       return view('dashboard',compact('bills','attendances','payments','member','status'));
   }
}
