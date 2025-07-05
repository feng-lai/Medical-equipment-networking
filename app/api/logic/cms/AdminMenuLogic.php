<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\AdminMenu;
use think\Exception;
use think\Db;

/**
 * 后台菜单-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AdminMenuLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['is_deleted'] = ['=',1];
    if($request['level']) $map['level'] = ['=',$request['level']];
    if($request['pid']) $map['pid'] = ['=',$request['pid']];
    $result = AdminMenu::build()->where($map)->order('serial_number asc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    foreach ($result as $v){
      if($v->pid){
        $v->p_name = AdminMenu::build()->where('uuid','=',$v->pid)->value('name');
      }else{
        $v->p_name = '';
      }
      $v->child = AdminMenu::build()->where('pid',$v['uuid'])->where('is_deleted','=',1)->order('level asc,serial_number asc')->select();
      foreach ($v->child as $k2 => $v2) {
         $v2->child= AdminMenu::build()->where('pid',$v2['uuid'])->where('is_deleted','=',1)->order('level asc,serial_number asc')->select();
      }
    }
    AdminLog::build()->add($userInfo['uuid'], '菜单','查询菜单列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  AdminMenu::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
    AdminLog::build()->add($userInfo['uuid'], '菜单','查看菜单详情：'.$data->name);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       $data = [
         'uuid' => uuid(),
         'name'=>$request['name'],
         'url'=>$request['url'],
         'pid'=>$request['pid'],
         'level'=>$request['level'],
         'serial_number'=>$request['serial_number'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       AdminMenu::build()->save($data);
       AdminLog::build()->add($userInfo['uuid'], '菜单','新增菜单：'.$data['name']);
       return $data['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = AdminMenu::build()->where('uuid', $request['uuid'])->find();
      $data->save($request);
      AdminLog::build()->add($userInfo['uuid'], '菜单','更新菜单：'.$data->name);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $data = AdminMenu::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '菜单','删除菜单：'.$data->name);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
