<?php

namespace App\Http\Controllers;


use App\Filament\Resources\MemberResource\RelationManagers\BillsRelationManager;
use App\Models\Attendance;
use App\Models\Bill;
use App\Models\Menu;
use App\Models\Payment;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuth extends Controller
{
    public function login(Request $request)
    {

        $creds = request(['email','password']);
        if(!Auth::attempt($creds))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = $request->user()->createToken('apiLoginToken');

        return ['token' => $token->plainTextToken,
                'name' => auth('sanctum')->user()->name,
        ];
    }

    public function memberBills()
    {
        if(!Auth('sanctum'))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $id = auth('sanctum')->user()->member->id ?? null;
        $bills = Bill::where('member_id',$id)->latest()->select('month','units','amount','status')->take(5)->get();
        return ['bills' => $bills];
    }

    public function memberAttendances()
    {
        if(!Auth('sanctum'))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $id = auth('sanctum')->user()->member->id ?? null;
        $attendances = Attendance::where('member_id',$id)
            ->whereBetween('date',[now()->startOfMonth()->format('Y-m-d'),now()->endOfMonth()->format('Y-m-d')])
            ->latest()->get();
        return response()->json(['attendances' => $attendances]);

    }

    public function memberPayments()
    {
        if(!Auth('sanctum'))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $id = auth('sanctum')->user()->member->id ?? null;
        $payments = Payment::where('member_id',$id)->latest()->take(5)->get();
        return response()->json(['payments' => $payments]);

    }

    public function accountBalance()
    {
        if(!Auth('sanctum'))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $bal = auth('sanctum')->user()->member->account_balance ?? null;
        return response()->json(['account_balance' => $bal]);
    }

    public function getMenu()
    {
//        if(!Auth('sanctum'))
//        {
//            return response()->json(['message' => 'Unauthorized'], 401);
//        }
        $menu = Menu::where('date',now()->format('Y-m-d'))->first();
        if(isset($menu))
        {
            return response()->json(['breakfast' => $menu->breakfast??"Usual",'lunch' => $menu->lunch??"No Lunch",'dinner' => $menu->dinner]);

        }
        else
        {
            return response()->json(['breakfast' => 'No Menu Set','lunch' => 'No Menu Set','dinner' => 'No Menu Set']);

        }
    }

}
