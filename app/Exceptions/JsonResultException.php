<?php

namespace App\Exceptions;

use Exception;
use Log;

class JsonResultException extends Exception
{

    public $code;
    public $msg;
    public $result;

    public function __construct($code,$msg,$result = null)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->result = $result;
    }

    public static function checkEmptyException($value, $code, $msg, $result = null){
        if(empty($value)){
            self::throwJsonResultException($code, $msg, $result);
        }
    }

    public static function checkZeroCountException($value, $code, $msg, $result = null){
        if(count($value) == 0){
            self::throwJsonResultException($code, $msg, $result);
        }
    }

    public static function checkExistException($value, $code, $msg, $result = null){
        if(!empty($value)){
            self::throwJsonResultException($code, $msg, $result);
        }
    }

    public static function throwJsonResultException($code,$msg,$result = null){
        Log::error('code = ' . $code);
        Log::error('msg = ' . $msg);
        Log::error('url = ' . $_SERVER['REQUEST_URI']);
        throw new JsonResultException($code, $msg, $result);
    }

    public static function checkEmptyHttpException($value,$httpErrorCode){

        if(empty($value)){
            abort($httpErrorCode);
        }

    }

    public static function checkModelExistException($class,$id){
        $model = $class::source()->show($id);

        self::checkEmptyHttpException($model,404);
    }
}
