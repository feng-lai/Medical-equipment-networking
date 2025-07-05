<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Area;
use app\api\model\Province;
use app\api\model\City;
use think\Exception;
use think\Db;

/**
 * 省份管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ProvinceLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['p.is_deleted'] = ['=',1];
    if($request['area_uuid']) $map['p.area_uuid'] = ['=',$request['area_uuid']];
    if($request['name']) $map['p.name'] = ['like','%'.$request['name'].'%'];
    $result = Province::build()
      ->field('p.uuid,p.name,a.name as area_name,p.area_uuid')
      ->alias('p')
      ->join('area a','a.uuid = p.area_uuid','left')
      ->where($map)
      ->order('p.create_time','desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    foreach ($result as $v){
      $v->city_num = City::build()->where('province_uuid',$v->uuid)->where('is_deleted',1)->count();
    }
    AdminLog::build()->add($userInfo['uuid'], '省份管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  Province::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '省份管理','查看详情：'.$data->name);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       if(Province::build()->where('name',$request['name'])->where('is_deleted',1)->count()){
         return ['msg'=>'省份已存在'];
       }
       $data = [
         'uuid' => uuid(),
         'area_uuid'=>$request['area_uuid'],
         'name'=>$request['name'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       Province::build()->save($data);
       AdminLog::build()->add($userInfo['uuid'], '省份管理','新增：'.$data['name']);
       return $data['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = Province::build()->where('uuid', $request['uuid'])->find();
      $data->save($request);
      AdminLog::build()->add($userInfo['uuid'], '省份管理','更新：'.$data->name);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $city_num = City::build()->where('province_uuid',$id)->where('is_deleted',1)->count();
       if($city_num){
         return ['msg'=>'该省份下辖城市存在数据，删除失败'];
       }
       $data = Province::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '省份管理','删除：'.$data->name);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
