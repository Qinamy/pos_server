<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\JsonResultException;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Services\CartService;
use App\Http\Controllers\Services\PaymentService;
use App\Utils\CurlUtils;
use Carbon\Carbon;
use Log;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Models\User;
use App\Http\Controllers\Services\GoodsService;
use App\Http\Controllers\Services\UtilService;
class PaymentController extends BaseController
{
    public function pay(Request $request)
    {

        // 生成订单
        
        Log::notice('@@'.PHP_EOL.PHP_EOL.'pay');
        
        Log::notice('@@'.PHP_EOL.PHP_EOL.$request->input('mobile'));

        $params = PaymentService::getParams($request,['mobile','pay_channel','pay_amt',
            'goods_title','goods_desc','auth_code','notify_url']);

        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));

        $params['order_no'] = 'P'.Carbon::now()->format('YmdHisu');
        $params['pay_amt'] = 0.01;

        $params['auth_code'] = '286116366733188779';

        $params['sign'] = UtilService::sign($params);



        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));

        $result = CurlUtils::getCurlData('http://ada.kuailai.me/api/payment/create', json_encode($params), null, '', ['Content-Type:application/json']);

        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($result));

    }

    public function callback()
    {
        //修改订单支付状态
        Log::notice('@@'.PHP_EOL.PHP_EOL.'callback');
        return  [
            'msg' => 'ok'
        ];
    }
}