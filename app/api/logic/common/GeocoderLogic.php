<?php

namespace app\api\logic\common;

use Exception;

/**
 * 用户端-用户登陆-获取位置信息
 * 
 * @author Yacon
 */
class GeocoderLogic
{
    public static function appAdd($params)
    {
        try {
            $result = getCityByLongLatByBaidu($params['longitude'], $params['latitude']);
            if (!array_key_exists('result', $result)) {
                return ['msg' => '定位失败'];
            }
            $result = $result['result']['addressComponent'];

            return $result;
        } catch (Exception $e) {
            return ['msg' => $e->getMessage()];
        }
    }
    static public function address($params){
        try {
          $result = address_to_lat($params);
          if (!array_key_exists('geocodes', $result) || $result['status'] == 0) {
            return ['msg' => '失败'];
          }
          $result = $result['geocodes'][0]['location'];
          return explode(',',$result);
        } catch (Exception $e) {
          return ['msg' => $e->getMessage()];
        }
    }
}
