<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAttendance extends Model
{
    use HasFactory;

    protected $fillable = ['date','is_lunch','is_dinner','member_id','is_lunch_taken','is_dinner_taken'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function units()
    {
        return $this->hasOne(DailyUnits::class,'date','date');
    }
}
