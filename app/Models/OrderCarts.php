<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCarts  extends Model
{
    protected $table='order_carts';

    protected $fillable = [
        'order_id',
        'goods_id',
        'goods_name',
        'price',
        'amount',
        'pay_price',
        'status',
    ];

}