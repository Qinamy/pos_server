<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\JsonResultException;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Services\CartService;
use App\Http\Controllers\Services\OrderService;
use App\Models\OrderCarts;
use Log;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Models\User;
use App\Http\Controllers\Services\GoodsService;
use App\Models\Order;
use DB;
use Carbon\Carbon;
class OrderController extends BaseController
{
    public function add(Request $request)
    {


        $params = OrderService::getParams($request,['shop_id','cashier_id','carts','price']);

        Log::notice('@@$params'.PHP_EOL.PHP_EOL.json_encode($params));
        if(!empty($params['msg'])){
            JsonResultException::throwJsonResultException(300,$params['msg']);
        }

        //todo carts中必传元素是否都传入了


        //todo 判断金额核对是否正确
        $goods_ids = collect($params['carts'])->map(function($item){
            Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($item));
            $cart = (object)$item;
            $cart->goods = (object)$cart->goods;
            return $cart->goods->goods_id;
        });

        $goods_list = Goods::whereIn('goods_id',$goods_ids)->get();
        $keyed_goods_list = $goods_list->keyBy(function($item){
            return $item->goods_id;
        });

        $price = 0;
        $insert_list = [];
        foreach ($params['carts'] as $cart_obj) {
            $cart = (object)$cart_obj;
            $cart->goods = (object)$cart->goods;
            $goods = $keyed_goods_list[$cart->goods->goods_id] ?? null;
            if(empty($goods))
                JsonResultException::throwJsonResultException(300,'系统中未录入'.$cart->goods->name.'商品，请重新录入');
            $price += $cart->amount * $goods->price ;

            $insert_list [] = [
                'goods_id' => $cart->goods->goods_id,
                'goods_name' => $cart->goods->name,
                'price' => $goods->price,
                'pay_price' => $goods->price,
                'amount' => $cart->amount,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
        }

        if($price != $params['price']) {
            Log::error('@@'.PHP_EOL.PHP_EOL.$price);
            Log::error('@@'.PHP_EOL.PHP_EOL.$params['price']);
            JsonResultException::throwJsonResultException(300, '价格核对错误');
        }


        //todo 判断是否有库存


        //todo 判断商品限制是否满足


        //todo 判断是否有优惠条件

        Log::notice('@@price'.PHP_EOL.PHP_EOL.$price);
        try {
            DB::beginTransaction();
            $order = Order::create([
                'order_no' => 'P'.Carbon::now()->format('YmdHisu'),
                'cashier_id' => $params['cashier_id'],
                'shop_id' => $params['shop_id'],
                'price' => $price,
                'pay_price' => $price,
                'status' => Order::localStatusActive
            ]);

            foreach ($insert_list as $key => $value) {
                $insert_list[$key] = array_merge($value, ['order_id' => $order->id]);
            }

            OrderCarts::insert($insert_list);

            DB::commit();
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return [
                'code' => 300,
                'msg' => '数据库忙，请稍后重试'
            ];
        }



        Log::notice('@@'.PHP_EOL.PHP_EOL.json_encode($params));
        return [
            'code' => 200,
            'order_no' => $order->order_no
        ];


    }
}