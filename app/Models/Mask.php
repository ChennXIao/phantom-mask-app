<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mask extends Model
{
    use HasFactory;

    protected $fillable = ['pharmacy_id', 'name', 'price', 'stock_quantity'];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
