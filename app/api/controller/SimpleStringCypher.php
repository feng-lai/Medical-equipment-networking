<?php
/**
 *
 */
namespace app\api\controller;


class SimpleStringCypher {
    public static function encrypt($input, $key){
//        var_dump($input);die;

        $key = SimpleStringCypher::reflowNormalBase64($key);
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
//        var_dump($size);
        $input = SimpleStringCypher::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, base64_decode($key), $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        $data = SimpleStringCypher::reflowURLSafeBase64($data);
        return $data;
    }
    public static function decrypt($sStr, $sKey){
        //sKey为密钥 S2
        $sStr = SimpleStringCypher::reflowNormalBase64($sStr);
        $sKey = SimpleStringCypher::reflowNormalBase64($sKey);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,
            base64_decode($sKey), base64_decode($sStr),
            MCRYPT_MODE_ECB);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
    private static function reflowURLSafeBase64($str){
        $str=str_replace("/","_",$str);
        $str=str_replace("+","-",$str);
        return $str;
    }
    private static function reflowNormalBase64($str){
        $str=str_replace("_","/",$str);
        $str=str_replace("-","+",$str);
        return $str;
    }
    private static function pkcs5_pad($text, $blocksize){
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
}