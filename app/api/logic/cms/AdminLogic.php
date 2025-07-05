<?php

namespace app\api\logic\cms;

use app\api\model\Admin;
use app\api\model\AdminToken;
use app\api\model\AdminRole;
use app\api\model\Area;
use think\Exception;
use think\Db;

/**
 * 后台用户-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AdminLogic
{
  static public function cmsList($request)
  {
    $map['a.is_deleted'] = ['=',1];
    if($request['name']) $map['a.name'] = ['like','%'.$request['name'].'%'];
    if($request['disabled']) $map['a.disabled'] = $request['disabled'];
    if($request['role_uuid']) $map['a.role_uuid'] = $request['role_uuid'];
    $result = Admin::build()
      ->field('a.uuid,a.name,a.mobile,a.last_login,r.name as role_name,a.type,a.area_uuid,a.disabled')
      ->alias('a')
      ->join('admin_role r','r.uuid = a.role_uuid','left')
      ->where($map)
      ->order('a.create_time desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    foreach ($result as $v){
      if($v->type == 2){
        $v->role_name = '超级管理员';
      }
      $area = Area::build()->where('uuid','in',$v->area_uuid)->column('name');
      $v->area = implode(',',$area);
    }

    return $result;
  }

  static public function cmsDetail($id)
  {
    return  Admin::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
  }

   static public function cmsAdd($request){
     try {
       Db::startTrans();
       AdminRole::build()->findOrFail($request['role_uuid']);
       if(Admin::build()->where('uname',$request['uname'])->where('is_deleted',1)->count()){
         return ['msg'=>'账号已存在'];
       }
       $data = [
         'uuid' => uuid(),
         'name'=>$request['name'],
         'password'=>md6($request['password']),
         'uname'=>$request['uname'],
         'mobile'=>$request['mobile'],
         'role_uuid'=>$request['role_uuid'],
         'area_uuid'=>$request['area_uuid'],
         'province_uuid'=>$request['province_uuid'],
         'city_uuid'=>$request['city_uuid'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       Admin::build()->save($data);
       //添加token
       $token = AdminToken::build();
       $token->uuid = uuid();
       $token->admin_uuid = $data['uuid'];
       $token->create_time = now_time(time());
       $token->update_time = now_time(time());
       $token->save();
       Db::commit();
       return $data['uuid'];
     } catch (Exception $e) {
       Db::rollback();
       throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request)
  {
    try {
      $user = Admin::build()->where('uuid', $request['uuid'])->find();
      if(isset($request['password'])){
        $request['password'] = md6($request['password']);
      }
      $user->save($request);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id){
     try {
       Admin::build()->where('uuid',$id)->update(['is_deleted'=>2]);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
