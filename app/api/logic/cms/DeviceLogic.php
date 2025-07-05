<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Device;
use app\api\model\Area;
use app\api\model\Heart;
use app\api\model\Province;
use app\api\model\City;
use think\Exception;
use app\api\logic\common\GeocoderLogic;
use think\Db;

/**
 * 设备管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class DeviceLogic
{
  static public function cmsList($request,$userInfo)
  {

    $map = [];
    if($request['area_uuid']) $map['area_uuid'] = ['=',$request['area_uuid']];
    if($request['sim']) $map['sim'] = ['like','%'.$request['sim'].'%'];
    if($request['hospital']) $map['hospital'] = ['=',$request['hospital']];
    if($request['status']) $map['status'] = ['=',$request['status']];
    if($request['state']) $map['state'] = ['=',$request['state']];
    if($request['type']) $map['type'] = ['=',$request['type']];
    if($request['state'] != 1 && $request['state']){
      $sim = get_sim($userInfo['uuid']);
      if($sim) $map['sim'] = ['in',$sim];
    }
    $result = Device::build()
      ->where($map)
      ->order('status','desc')
      ->order('create_time','desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    foreach($result as $v){
      $v->v = $v->top_v.'/'.$v->btm_v;
      $v->cal_par = Heart::build()->where('sim',$v->sim)->whereNotNull('cal_par')->order('create_time','desc')->value('cal_par');
    }
    AdminLog::build()->add($userInfo['uuid'], '设备管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  Device::build()
      ->field('d.*,a.name as area_name,p.name as province_name,c.name as city_name')
      ->alias('d')
      ->where('d.uuid', $id)
      ->join('area a','d.area_uuid = a.uuid','left')
      ->join('province p','d.province_uuid = p.uuid','left')
      ->join('city c','d.city_uuid = c.uuid','left')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '设备管理','查看详情：'.$data->sim);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       if(Device::build()->where('sim',$request['sim'])->count()){
         return ['msg'=>'设备已存在'];
       }
       $request['create_time'] = now_time(time());
       $request['update_time'] = now_time(time());
       $request['uuid'] = uuid();
       //获取经纬度
       $result = GeocoderLogic::address($request['address']);
       if(!isset($result['msg'])){
         $request['lgt'] = $result[0];
         $request['lat'] = $result[1];
       }
       Device::build()->save($request);
       AdminLog::build()->add($userInfo['uuid'], '设备管理','新增：'.$request['sim']);
       return $request['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      unset($request['area_name']);
      unset($request['province_name']);
      unset($request['city_name']);
      $data = Device::build()->where('uuid', $request['uuid'])->find();
      if(isset($request['state'])){
        if($request['state'] == 3){
          $request['disabled_time'] = now_time(time());
        }
      }
      if(isset($request['address'])){
        $result = GeocoderLogic::address($request['address']);
        if(!isset($result['msg'])){
          $request['lgt'] = $result[0];
          $request['lat'] = $result[1];
        }
      }
      $data->save($request);

      AdminLog::build()->add($userInfo['uuid'], '设备管理','更新：'.$data->sim);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $data = Device::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '设备管理','删除：'.$data->sim);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
