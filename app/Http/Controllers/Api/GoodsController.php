<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\JsonResultException;
use App\Http\Controllers\BaseController;
use Log;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Models\User;
use App\Http\Controllers\Services\GoodsService;
class GoodsController extends BaseController
{
    public function query(Request $request)
    {

        $params = GoodsService::getParams($request,['barcode','mobile']);

        if(!empty($params['msg'])){
            JsonResultException::throwJsonResultException(300,$params['msg']);
        }


        //todo 将$user_id的获取过程放到middleware中
        $user = User::where('mobile',$params['mobile'])->first();

        JsonResultException::checkEmptyException($user,300,'该用户未注册');

        $goods = Goods::where('user_id',$user->id)->where('barcode',$params['barcode'])->first();

        JsonResultException::checkEmptyException($goods,300,'未添加该条码商品');

        return [
            'code' => 200,
            'result' => $goods
        ];
    }

    public function add(Request $request)
    {

//        $params = GoodsService::getParams($request,['goods','mobile','shop_id']);
        $params = $request->only(['goods','mobile','shop_id']);

        //判断goods对象中的必传属性是否传入了

        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));

//        Log::notice('@@'.PHP_EOL.PHP_EOL.$params['goods']->price);
        Log::notice('@@'.PHP_EOL.PHP_EOL.$params['goods']['price']);


//        if(!empty($params['msg'])){
//            JsonResultException::throwJsonResultException(300,$params['msg']);
//        }
//
//        if(empty($request->input('mobile')))
//            JsonResultException::throwJsonResultException(300,'请输入账号');
//        if(empty($request->input('shop_id')))
//            JsonResultException::throwJsonResultException(300,'请输入商铺编号');
        if(empty($params['goods']['price']))
            JsonResultException::throwJsonResultException(300,'请输入商品价格');

        if(empty($params['goods']['barcode']))
            JsonResultException::throwJsonResultException(300,'请输入商品条码');


        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params['goods']));

//        Log::notice('@@'.PHP_EOL.PHP_EOL.$params['mobile']);

//        //todo 将$user_id的获取过程放到middleware中
//        $user = User::where('mobile',$params['mobile'])->first();
//
//        JsonResultException::checkEmptyException($user,300,'该用户未注册');
//
        $goods = Goods::where('barcode',$params['goods']['barcode'])->first();
//
        $barcode_length = strlen($params['goods']['barcode']);
        $temp_name = '条码尾号'.substr($params['goods']['barcode'],$barcode_length-6,6);
        if(empty($goods)){
            Goods::create([
//                'user_id' => $user->id,
                'barcode' => $params['goods']['barcode'],
//                'shop_id' => $params['shop_id'],
                'name' => $params['goods']['name'] ?? $temp_name,
                'price' => $params['goods']['price'],
//                'thumb' => $params['goods']['thumb']
            ]);
        }
        else{
            JsonResultException::throwJsonResultException(300,'已添加过该商品，不可重复添加');
        }



    }
}