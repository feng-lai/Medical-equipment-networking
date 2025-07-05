<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Area;
use app\api\model\Province;
use app\api\model\City;
use think\Exception;
use think\Db;

/**
 * 区域管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AreaLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['is_deleted'] = ['=',1];
    if($request['disabled']) $map['disabled'] = ['=',$request['disabled']];
    if($request['name']) $map['name'] = ['like','%'.$request['name'].'%'];
    $result = Area::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    foreach ($result as $v){
      $v->province_num = Province::build()->where('area_uuid',$v->uuid)->where('is_deleted',1)->count();
      $v->city_num = City::build()->where('area_uuid',$v->uuid)->where('is_deleted',1)->count();
    }
    AdminLog::build()->add($userInfo['uuid'], '区域管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  Area::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '区域管理','查看详情：'.$data->name);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       if(Area::build()->where('name',$request['name'])->where('is_deleted',1)->count()){
         return ['msg'=>'区已存在'];
       }
       $data = [
         'uuid' => uuid(),
         'name'=>$request['name'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       Area::build()->save($data);
       AdminLog::build()->add($userInfo['uuid'], '区域管理','新增：'.$data['name']);
       return $data['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = Area::build()->where('uuid', $request['uuid'])->find();
      $data->save($request);
      AdminLog::build()->add($userInfo['uuid'], '区域管理','更新：'.$data->name);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $province_num = Province::build()->where('area_uuid',$id)->where('is_deleted',1)->count();
       $city_num = City::build()->where('area_uuid',$id)->where('is_deleted',1)->count();
       if($province_num || $city_num){
         return ['msg'=>'该区域下辖省份或者下辖城市存在数据，删除失败'];
       }
       $data = Area::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '区域管理','删除：'.$data->name);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
