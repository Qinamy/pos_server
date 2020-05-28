<?php


namespace App\Http\Controllers\Services;
use Log;
use App\Http\Controllers\Services\UtilService;

class GoodsService
{

    public static $all_param_names = ['barcode','mobile','goods','shop_id'];


    public static function getParams($request,$necessary_param_names,$all_param_names = null)
    {

        Log::notice('@@'.PHP_EOL.PHP_EOL.'haha');
        return UtilService::getParams($request,$necessary_param_names,$all_param_names ?? self::$all_param_names);

    }

    public static function index()
    {
        Log::notice('@@'.PHP_EOL.PHP_EOL.'gaga');
    }



}