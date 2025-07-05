<?php

namespace app\api\logic\mini;

use think\Exception;
use think\Db;
use app\api\model\Device;

/**
 * 设备-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class DeviceLogic
{

  static public function miniList($request)
  {
    $map = [];
    if($request['hospital']) $map['hospital'] = ['like','%'.$request['hospital'].'%'];
    if($request['sim']) $map['sim'] = $request['sim'];
    if($request['sim']){
      $data =  Device::build()
        ->field('hospital,address,sim')
        ->where($map)
        ->find();
    }else{
      $data =  Device::build()
        ->field('hospital,address,sim')
        ->where($map)
        ->select();
    }

    return $data;
  }
  static public function miniDetail($id)
  {
    $data =  Device::build()
      ->where('sim', $id)
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    return $data;
  }

  static public function miniAdd($request)
  {
    $map = [];
    if($request['hospital']) $map['hospital'] = ['like','%'.$request['hospital'].'%'];
    if($request['sim']) $map['sim'] = $request['sim'];
    $data =  Device::build()
      ->field('hospital,address,sim')
      ->where($map)
      ->find();
    return $data;
  }
}
