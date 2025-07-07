<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cash_balance',
    ];

    public function masks()
    {
        return $this->hasMany(Mask::class);
    }

    public function hours()
    {
        return $this->hasMany(PharmacyHour::class);
    }
}
