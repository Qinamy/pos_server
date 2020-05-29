<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utils;
use Illuminate\Support\Facades\Log;

class CurlUtils{
    
    public static function getCurlData($url,$postData = '',$cookie = null,$referer = '',$header = null){
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );

        if(!empty($postData)){
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
        }

        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }


        
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        curl_setopt ( $ch,  CURLOPT_REFERER , $referer);
        
        if($header == null){
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        }else{
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec ( $ch );

        $info = curl_getinfo($ch);
        Log::notice($info);

        curl_close ( $ch );
        
        return $data;
    }
    
}
