<?php


namespace App\Http\Controllers\Services;
use Log;

class OrderService
{

    public static $all_param_names = ['shop_id','cashier_id','carts','price'];

    const property_translate = [
        'price' => '售价',
        'barcode' => '条码'
    ];

    public static function getParams($request,$necessary_param_names,$all_param_names = null)
    {

        return UtilService::getParams($request,$necessary_param_names,$all_param_names ?? self::$all_param_names);

    }

    public static function index()
    {
        Log::notice('@@'.PHP_EOL.PHP_EOL.'gaga');
    }

    public static function checkGoodsAddParams($goods,$properties)
    {
        $msg = '';
        foreach($properties as $property){
            if(empty($goods[$property])){
                $msg= ( empty($msg) ? '' : $msg.',' ). self::property_translate[$property];
            }
        }
        if(!empty($msg))
            $msg = '请输入'.$msg;

        return $msg;
    }



}