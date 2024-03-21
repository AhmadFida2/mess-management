<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name'];
    use HasFactory;


    public function payments()
    {
        return $this->belongsToMany(Payment::class);
    }
}
