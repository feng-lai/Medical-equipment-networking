<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\AdminRole;
use app\api\model\AdminMenu;
use think\Exception;
use think\Db;

/**
 * 后台菜单-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AdminRoleLogic
{
  static public function cmsList($userInfo)
  {
    $model = AdminRole::build();
    $list = $model->where('is_deleted','=',1)->order('create_time desc')->select();
    AdminLog::build()->add($userInfo['uuid'], '角色','查询角色列表');
    return $list;
  }

  static public function cmsDetail($id,$userInfo)
  {
    $data =  AdminRole::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
    AdminLog::build()->add($userInfo['uuid'], '角色','查询角色详情：'.$data->name);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       $data = [
         'uuid' => uuid(),
         'name'=>$request['name'],
         'menus'=>$request['menus'],
         'serial_number'=>$request['serial_number'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       AdminRole::build()->save($data);
       AdminLog::build()->add($userInfo['uuid'], '角色','新增角色：'.$data['name']);
       return $data['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = AdminRole::build()->where('uuid', $request['uuid'])->find();
      $data->save($request);
      AdminLog::build()->add($userInfo['uuid'], '角色','更新角色：'.$data->name);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $data = AdminRole::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '角色','删除角色：'.$data->name);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
