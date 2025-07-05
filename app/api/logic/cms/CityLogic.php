<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Area;
use app\api\model\Province;
use app\api\model\City;
use think\Exception;
use think\Db;

/**
 * 城市管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class CityLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['c.is_deleted'] = ['=',1];
    if($request['area_uuid']) $map['c.area_uuid'] = ['=',$request['area_uuid']];
    if($request['province_uuid']) $map['c.province_uuid'] = ['=',$request['province_uuid']];
    if($request['name']) $map['c.name'] = ['like','%'.$request['name'].'%'];
    $result = City::build()
      ->field('c.uuid,c.name,a.name as area_name,p.name as province_name,c.area_uuid,c.province_uuid')
      ->alias('c')
      ->join('area a','c.area_uuid = a.uuid','left')
      ->join('province p','c.province_uuid = p.uuid','left')
      ->where($map)
      ->order('c.create_time','desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '城市管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  City::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '城市管理','查看详情：'.$data->name);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       if(City::build()->where('name',$request['name'])->where('is_deleted',1)->count()){
         return ['msg'=>'市已存在'];
       }
       $data = [
         'uuid' => uuid(),
         'area_uuid'=>$request['area_uuid'],
         'province_uuid'=>$request['province_uuid'],
         'name'=>$request['name'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       City::build()->save($data);
       AdminLog::build()->add($userInfo['uuid'], '城市管理','新增：'.$data['name']);
       return $data['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = City::build()->where('uuid', $request['uuid'])->find();
      $data->save($request);
      AdminLog::build()->add($userInfo['uuid'], '城市管理','更新：'.$data->name);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $data = City::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '城市管理','删除：'.$data->name);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
