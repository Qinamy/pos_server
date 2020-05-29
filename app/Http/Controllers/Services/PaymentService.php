<?php


namespace App\Http\Controllers\Services;
use Log;

class PaymentService
{

    public static $all_param_names = ['mobile','pay_channel','pay_amt',
            'goods_title','goods_desc','auth_code','notify_url'
        ];

    const property_translate = [
        'mobile' => '手机号',
        'pay_channel' => '支付渠道',
        'pay_amt' => '支付金额',
        'goods_title' => '商品标题',
        'goods_desc' => '商品描述信息',
        'auth_code' => '付款码',
        'notify_url' => '回调地址',
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

//    public static function add($params)
//    {
//    }



}