<?php

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login',[\App\Http\Controllers\ApiAuth::class,'login']);

Route::get('/bills',[\App\Http\Controllers\ApiAuth::class,'memberBills']);

Route::get('/attendances',[\App\Http\Controllers\ApiAuth::class,'memberAttendances']);

Route::get('/payments',[\App\Http\Controllers\ApiAuth::class,'memberPayments']);

Route::get('/balance',[\App\Http\Controllers\ApiAuth::class,'accountBalance']);

Route::get('/menu',[\App\Http\Controllers\ApiAuth::class,'getMenu']);


//::middleware('auth:sanctum')->resource('attendances',\App\Http\Controllers\AttendanceController::class);

Route::get('/members',function (){

    return Member::pluck('name','id');
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
