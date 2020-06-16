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

    public function sync(Request $request)
    {

        $params = GoodsService::getParams($request,['shop_id','cashier_id']);

        if(!empty($params['msg'])){
            JsonResultException::throwJsonResultException(300,$params['msg']);
        }

        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));

        //todo 判断cashier_id 是否有shop_id的权限

        $goods_list = Goods::where('shop_id',$request->input('shop_id'))->get();

        foreach ($goods_list as $item) {
            //传到前台的price以元为单位，保留两位有效数字，以适配数字键盘
            $item->price /= 100;
        }

        return [
            'code' => 200,
            'result' => $goods_list
        ];
    }

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

    public function modify(Request $request)
    {
        $params = GoodsService::getParams($request,['goods','cashier_id','shop_id']);

        if(!empty($params['msg'])){
            JsonResultException::throwJsonResultException(300,$params['msg']);
        }

        //判断goods对象中的必传属性是否传入了
        $msg = GoodsService::checkGoodsAddParams($params['goods'],['price','barcode','goods_id','name']);
        if(!empty($msg)){
            JsonResultException::throwJsonResultException(300,$msg);
        }

        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));

        $goods = Goods::where('goods_id',$params['goods']['goods_id'])->first();
        if(empty($goods)){
            Goods::create([
                'cashier_id' => $params['cashier_id'],
                'shop_id' => $params['shop_id'],
                'barcode' => $params['goods']['barcode'],
                'goods_id' => $params['goods']['goods_id'],
                'name' => $params['goods']['name'] ,
                'price' => $params['goods']['price'] * 100,
                'thumb' => $params['goods']['thumb'] ?? ''
            ]);
        }
        else{
            $goods->price = $params['goods']['price'] * 100;
            $goods->name = $params['goods']['name'];
            $goods->thumb = $params['goods']['thumb'];
            $goods->save();
        }

    }
}