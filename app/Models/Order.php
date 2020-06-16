<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order  extends Model
{
    protected $table='orders';

    protected $fillable = [
        'cashier_id',
        'shop_id',
        'price',
        'pay_price',
        'discount_price',
        'remark',
        'status',
    ];

    const localStatusActive = 1;

}