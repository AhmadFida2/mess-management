<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyUnits extends Model
{
    use HasFactory;

     protected $fillable = ['date','lunch','dinner'];
}
