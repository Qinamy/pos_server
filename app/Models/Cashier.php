<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cashier  extends Model
{
    use SoftDeletes;
    protected $table='cashiers';

    protected $fillable = [
        'id',
        'shop_id',
        'name',
        'mobile',
        'status',
    ];

    const localStatusActive = 1;

}