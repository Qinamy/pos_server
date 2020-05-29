<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\JsonResultException;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Services\CartService;
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
        $params = GoodsService::getParams($request,['goods','mobile','shop_id']);

        if(!empty($params['msg'])){
            JsonResultException::throwJsonResultException(300,$params['msg']);
        }

        //判断goods对象中的必传属性是否传入了
        $msg = GoodsService::checkGoodsAddParams($params['goods'],['price','barcode']);
        if(!empty($msg)){
            JsonResultException::throwJsonResultException(300,$msg);
        }

        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));

        //判断mobile是否有该shop_id的权限

        $user = User::where('mobile',$params['mobile'])->first();
        $goods = Goods::where('barcode',$params['goods']['barcode'])->first();
        $barcode_length = strlen($params['goods']['barcode']);
        $temp_name = '条码尾号'.substr($params['goods']['barcode'],$barcode_length-6,6);
        if(empty($goods)){
            Goods::create([
                'user_id' => $user->id,
                'barcode' => $params['goods']['barcode'],
                'shop_id' => $params['shop_id'],
                'name' => $params['goods']['name'] ?? '未命名',
                'price' => $params['goods']['price'],
                'thumb' => $params['goods']['thumb'] ?? ''
            ]);
        }
        else{
            $goods->price = $params['goods']['price'];
            $goods->save();
//            JsonResultException::throwJsonResultException(300,'已添加商品不可重复添加');
        }

        //将该商品添加到购物车中
//        CartService::add($params);

    }
}