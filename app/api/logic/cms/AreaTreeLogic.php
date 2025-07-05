<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Area;
use app\api\model\Province;
use app\api\model\City;
use think\Exception;
use think\Db;

/**
 * 区域树管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AreaTreeLogic
{
  static public function cmsList($request)
  {
    $map['is_deleted'] = ['=',1];
    $map['disabled'] = ['=',1];
    $result = Area::build()->where($map)->order('create_time','desc')->select();
    foreach ($result as $v){
      $province = Province::build()->where('area_uuid',$v->uuid)->where('is_deleted',1)->order('create_time','desc')->select();
      if($province){
        foreach($province as $val){
          if(!$request['hide_city']){
            $val->child = City::build()->where('area_uuid',$v->uuid)->where('province_uuid',$val->uuid)->where('is_deleted',1)->order('create_time','desc')->select();
          }
        }
        $v->child = $province;
      }
    }
    return $result;
  }

}
