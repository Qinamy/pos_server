<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $table='goods';

    protected $fillable = [
        'goods_id',
        'cashier_id',
        'barcode',
        'shop_id',
        'name',
        'price',
        'thumb'
    ];

    protected $casts = [
        'price' => 'string',
        'barcode' => 'string'
    ];

}