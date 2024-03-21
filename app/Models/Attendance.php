<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\CssSelector\Node\AttributeNode;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['date','meal','units','member_id'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }


}
