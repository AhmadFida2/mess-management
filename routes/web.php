<?php


use App\Models\Attendance;
use App\Models\Bill;
use App\Models\DailyAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',function (){
return redirect('/portal');
});

Route::get('/printAttendance', function (){
    abort_unless(auth()->user()->is_admin || auth()->user()->is_staff,401);
    $attendances = DailyAttendance::where('date',now()->format('Y-m-d'))->get();
    return view('attendances',compact('attendances'));
})->name('attendance-print');

Route::get('/print/{id}', function ($id){
            $status = ['Unpaid','Partial Paid','Paid'];
            $bill = Bill::find($id);
            if(!isset($bill))  abort(401);
           if(!(auth()->user()->is_admin))
           {
               $mid = auth()->user()->member->id;
               if(!isset($mid)) abort(401);
               $bid = $bill->member_id;

               abort_unless($mid===$bid,401);
           }
            $paid_amount = DB::table('payments')->where('bill_id',$id)->sum('amount');
            $date = Carbon::parse($bill->month)??"";
            $startDate = $date->startOfMonth()->format('Y-m-d');
            $endDate = $date->endOfMonth()->format('Y-m-d');
            $unit_cost = \App\Models\UnitCost::where('month',$startDate)->first()->cost;
            $attendances = DB::table('attendances')
                ->where('member_id','=',$bill->member_id)
                ->whereBetween('date',[$startDate,$endDate])
                ->get();
        return view('dashboard',compact('bill','attendances','date','status','unit_cost','paid_amount'));
})->name('bill-print');

Route::get('/install/{seed}', function ($seed){
    Artisan::call('db:wipe', ["--force"=> true ]);
    Artisan::call('migrate:fresh', ["--force"=> true ]);
    // Create Super-Admin
    $user = new User([
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => \Illuminate\Support\Facades\Hash::make('admin'),
        'is_admin' => true
    ]);
    $user->save();
    // Create Staff
    $user = new User([
        'name' => 'Staff',
        'email' => 'staff@staff.com',
        'password' => \Illuminate\Support\Facades\Hash::make('staff'),
        'is_staff' => true
    ]);
    $user->save();
    if($seed)
    {
        set_time_limit(300);
        Artisan::call('db:seed', ["--force"=> true ]);
    }
    return redirect('/portal');
});
