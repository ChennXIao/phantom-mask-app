<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PharmacyHour extends Model
{
    use HasFactory;

    protected $fillable = ['pharmacy_id', 'weekday', 'open_time', 'close_time'];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
