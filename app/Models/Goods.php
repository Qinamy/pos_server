<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $table='goods';

    protected $fillable = [
        'user_id',
        'barcode',
        'shop_id',
        'name',
        'price',
        'thumb'
    ];

}