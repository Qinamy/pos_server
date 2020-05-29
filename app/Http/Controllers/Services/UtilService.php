<?php


namespace App\Http\Controllers\Services;
use App\Model\Merchant;
use App\Model\Member;
use App\Model\Region;
use App\Model\SettleAccount;
use DB;
use Log;

class UtilService
{

    const SECRET = 'bbf5a23972c74861345d9f1ef1331db0';

    const property_translate = [
        'mobile' => '手机号',
        'shop_id' => '商铺Id'
    ];

    /**
     * @param $request  //读取所有输入参数
     * @param $necessary_param_names    //必须传入的参数
     * @param $all_param_names  //所有可能用到的参数
     * @return array    //  成功：返回参数数组   失败：返回错误信息
     */
    public static function getParams($request,$necessary_param_names,$all_param_names,$property_translate = null)
    {
        $params = $request->only($all_param_names);

        $msg = '';
        foreach($necessary_param_names as $name)
            if(empty($params[$name]))
                $msg= ( empty($msg) ? '' : $msg.',' ). $property_translate[$name];

        if(!empty($msg))
            return [
                'msg' => '请输入'.$msg
            ];


        //对于选填的传入属性要做空值处理，否则报Undefined index错误
        foreach($all_param_names as $name)
            if(empty($params[$name]))
                $params[$name] = '';

        return $params;
    }

    public static function getFailedReturn($result)
    {
        return  [
            'status' => $result['status'] ?? '',
            'error_code' => $result['error_code'] ?? '',
            'error_msg' => $result['error_msg'] ?? '',
            'error_type' => $result['error_type'] ??'',
            'invalid_param' => $result['invalid_param'] ?? '',

        ];
    }

    /**
     * @param $result
     * @param $params   //返回的参数数组
     * @return array
     */
    public static function getSucceededReturn($result,$params)
    {
        $return_array = [];
        foreach($params as $param){

            if($param == 'pay_url'){
                $return_array[$param] = $result['expend'][$param];
                continue;
            }

            //settleaccount/create 返回的是create_time ， 统一返回成created_time
            if($param == 'created_time' && empty($result[$param]) ){
                if(!empty($result['create_time'])){
                    $return_array[$param] = $result['create_time'];

                }
            }
            else
                $return_array[$param] = $result[$param];
        }
        return $return_array;
    }

    public static function getReturn($result,$params)
    {
        if($result['status'] == 'failed')
            return self::getFailedReturn($result);
        else
            return self::getSucceededReturn($result,$params);
    }

    public static function getRsa(&$private_key,&$public_key )
    {
        $res=openssl_pkey_new(array('private_key_bits' => 1024));
        // Get private key
        openssl_pkey_export($res, $private_key);

        $private_key = self::trimRsa($private_key);

        // Get public key
        $public_key=openssl_pkey_get_details($res);
        $public_key=$public_key["key"];
        $public_key = self::trimRsa($public_key);

    }

    public static function trimRsa($key)
    {
        $key_array = explode('-----',$key);
        return trim($key_array[2]);
    }

    public static function getBank($bank_name)
    {
        //读取文件将银行信息导入进去
        $bank_list = DB::table('bank')->where('bank_name','like','%'.$bank_name.'%')->get();
        if(count($bank_list) == 0){
            return [
                'error_msg' => '银行名称不正确'
            ];
        }
        else if(count($bank_list) > 1){
            return [
                'error_msg' => '请输入确切的银行名称'
            ];
        }
        else {
            return [
                'bank_code' => $bank_list[0]->bank_code,
                'bank_name' => $bank_list[0]->bank_name,
            ];
        }
    }

    public static function getDistrict($district_name,$city_name)
    {
        $region_list = DB::table('region as district')->leftJoin('region as city','district.parent_code','=','city.code')
            ->where('district.status',Region::localStatusDistrict)
            ->where('district.name','like','%'.$district_name.'%')
            ->where('city.name','like','%'.$city_name.'%')
            ->select('district.*')
            ->get();

        if(count($region_list) == 0){
            return [
                'error_msg' => '市、区名称不正确'
            ];
        }
        else if(count($region_list) > 1){
            return [
                'error_msg' => '请输入确切的市、区名称'
            ];
        }
        else {
            return [
                'district_six_code' => $region_list[0]->code,
            ];
        }
    }

    public static function sign($array)
    {
        $array['secret'] = self::SECRET;
        ksort($array);
        $str = '';
        $index = 0;
        foreach($array as $key=>$value){
            if($index++ > 0)
                $str .= '&';
            $str.=$key.'='.$value;
        }

        return md5($str);
    }


    public static function getHashSign($array)
    {

        unset($array['sign']);

        ksort($array);
        $query = self::ToUrlParams($array);

        Log::error($query);

        $gen_sign = hash_hmac('sha256', $query, self::sign_key);

        return $gen_sign;
    }

    public static function ToUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . rawurlencode($v) . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }


    public static function checkParams($params,$necessary_param_names)
    {
        foreach($necessary_param_names as $name)
            if(empty($params[$name]))
                return false;

        return true;
    }


}