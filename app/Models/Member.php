<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable =['name','email','mobile','user_id','join_date','is_active','account_balance','security'];

    protected $casts =['is_active' => 'boolean'];


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

public function bills()
{
    return $this->hasMany(Bill::class);
}

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
