<?php

namespace app\api\logic\mini;

use app\api\model\AdminLog;
use app\api\model\ServiceLog;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 维修日志-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ServiceLogLogic
{
  static public function cmsAdd($request){
    try {
      /**
      if($request['device_img'] && count($request['device_img'])){
        $img = json_decode($request['device_img'][0]);
        $arr = [];
        foreach($img as $v){
          $res = explode('/',$v);
          $arr[] = ['url'=>$v,'name'=>end($res)];
        }
        $request['device_img'] = $arr;
      }**/
      if($request['device_img'] && count($request['device_img'])){
        $arr = [];
        foreach($request['device_img'] as $v){
          $res = explode('/',$v);
          $arr[] = ['url'=>$v,'name'=>end($res)];
        }
        $request['device_img'] = $arr;
      }
      /**
      $request['sim'] = json_decode($request['sim'])[0];
      $request['hospital'] = json_decode($request['hospital'])[0];
      $request['hospital_address'] = json_decode($request['hospital_address'])[0];
      $request['hospital_contact'] = json_decode($request['hospital_contact'])[0];
      $request['device_snum'] = json_decode($request['device_snum'])[0];
      $request['device_v'] = json_decode($request['device_v'])[0];
      $request['reagent_snum'] = json_decode($request['reagent_snum'])[0];
      $request['reagent_lot_num'] = json_decode($request['reagent_lot_num'])[0];
      $request['day_min'] = json_decode($request['day_min'])[0];
      $request['day_max'] = json_decode($request['day_max'])[0];
      $request['year_min'] = json_decode($request['year_min'])[0];
      $request['year_max'] = json_decode($request['year_max'])[0];
      $request['rate'] = json_decode($request['rate'])[0];
      $request['dsc'] = json_decode($request['dsc'])[0];
      $request['b_fail_operate'] = json_decode($request['b_fail_operate'])[0];
      $request['handle'] = json_decode($request['handle'])[0];
      $request['sales_sevice_freg'] = json_decode($request['sales_sevice_freg'])[0];
       * **/
      $request['uuid'] = uuid();
      ServiceLog::build()->save($request);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }
}
