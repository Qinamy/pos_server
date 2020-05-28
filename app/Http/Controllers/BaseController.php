<?php

namespace App\Http\Controllers;

define('GLOBAL_TYPE_HTML', 'html');
define('GLOBAL_TYPE_JSON', 'json');
define('GLOBAL_TYPE_JSONP', 'jsonp');
define('GLOBAL_TYPE_DOWNLOAD', 'download');
define('GLOBAL_TYPE_FILE', 'file');
define('GLOBAL_TYPE_APP', 'app');

define('GLOBAL_RESPONSE_MSG', 'msg');
define('GLOBAL_RESPONSE_CODE', 'code');
define('GLOBAL_RESPONSE_RESULT', 'result');
define('GLOBAL_RESPONSE_TYPE', 'global_type');

define('GLOBAL_RESPONSE_VALUE_200', '200');
define('GLOBAL_RESPONSE_VALUE_300', '300');
define('GLOBAL_RESPONSE_VALUE_OKEY', 'okay');
define('GLOBAL_RESPONSE_VALUE_ERROR', 'error');
define('GLOBAL_RESPONSE_VALUE_NO_PERMISSION', '权限不足');
define('GLOBAL_RESPONSE_VALUE_NOT_FULL', 'error:not full');
define('GLOBAL_RESPONSE_VALUE_ADD_ERROR', 'error:add ');
define('GLOBAL_RESPONSE_VALUE_UPDATE_ERROR', 'error:update');
define('GLOBAL_RESPONSE_VALUE_DELETE_ERROR', 'error:delete');
define('GLOBAL_RESPONSE_VALUE_COUNT_NOT_ZENO', 'error:count zero');

class BaseController extends Controller
{

    private $_code,$_msg;

    private $_response_type,$_response_value,$_templates,$_request_value;
    private $_response_file_path,$_response_file_name;
    public $_result;



    public function setCode($code){
        $this->_code = $code;
    }
    public function getCode()
    {
        return $this->_code;
    }
    public function setMsg($msg){
        $this->_msg = $msg;
    }
    public function getMsg(){
        return $this->_msg;
    }

    public function setResponseType($type){
        $this->_response_type = $type;
    }
    public function getResponseType()
    {
        return $this->_response_type;
    }
    public function getTemplates()
    {
        return $this->_templates;
    }
    public function setTemplates($templates)
    {
        $this->_templates = $templates;
    }
    public function getResponseValue(){
        return $this->_response_value;
    }
    public function setResponseValue($response_value){
        $this->_response_value = $response_value;
    }
    public function getRequestValue() {
        return $this->_request_value;
    }
    public function setRequestValue($request){
        $this->_request_value = $request;
    }
    public function showResponse(){
        $response['result'] = $this->_result;
        return $this->showView($response);
    }

    public function setFileResponse($path,$name){
        $this->_response_file_path = $path;
        $this->_response_file_name = $name;
    }

    public function showView($response,$templates = null) {

        if ($this->_response_type == GLOBAL_TYPE_HTML) {

            return view($templates, $response);
        } else if ($this->_response_type == GLOBAL_TYPE_JSON) {
            $response['code'] = $this->_code;
            $response['msg'] = $this->_msg;
            return response()->json($response);
        }else if ($this->_response_type == GLOBAL_TYPE_DOWNLOAD) {
            $response['code'] = $this->_code;
            $response['msg'] = $this->_msg;
            if($this->_code == GLOBAL_RESPONSE_VALUE_300){
                return view($templates, $response);
            }

            return;
        }else if ($this->_response_type == GLOBAL_TYPE_FILE) {
            $response['code'] = $this->_code;
            $response['msg'] = $this->_msg;
            if($this->_code == GLOBAL_RESPONSE_VALUE_300){
                return view($templates, $response);
            }

            if(empty($this->_response_file_name) || empty($this->_response_file_path)){
                abort(404);
            }

            return response()
                ->download($this->_response_file_path, $this->_response_file_name);
        }else{
            abort(404);
        }
    }

    public function appendsFilter($list,$filter){
        foreach($filter as $key => $value){
            $list->appends($key,$value);
        }
    }

}
