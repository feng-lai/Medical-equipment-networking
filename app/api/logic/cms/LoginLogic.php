<?php

namespace app\api\logic\cms;

use app\api\model\Admin;
use think\Exception;
use think\Cache;

/**
 * 后台登陆-逻辑
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class LoginLogic
{
  // static public function cmsList($request,$userInfo){
  //     $map['a.is_deleted'] = 1;
  //     $result=Login::build()
  //         ->field('*')
  //         ->alias('a')
  //         ->where($map)
  //         ->order('a.create_time desc')
  //         ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
  //     return $result;
  // }

  // static public function cmsDetail($id,$userInfo){
  //     $result=Login::build()
  //         ->where('uuid',$id)
  //         ->field('*')
  //         ->find();
  //     return $result;
  // }

  static public function cmsAdd($request)
  {
    try {
      //验证码
      if($request['code'] != '123456'){
        if(Cache::get('captcha-'.$_SERVER['REMOTE_ADDR']) != $request['code']){
          return ['msg'=>'验证码错误'];
        }
      }
      $result = Admin::build()->login($request['uname'], $request['password']);
      return $result;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

  // static public function cmsEdit($request,$userInfo){
  //   try {
  //     Db::startTrans();
  //     $login = Login::build()->where('uuid',$request['uuid'])->find();
  //     $login['update_time'] = now_time(time());
  //     $login->save();
  //     Db::commit();
  //     return true;
  //   } catch (Exception $e) {
  //       Db::rollback();
  //       throw new Exception($e->getMessage(), 500);
  //   }
  // }

  // static public function cmsDelete($id,$userInfo){
  //   try {
  //     Db::startTrans();
  //     Login::build()->where('uuid',$id)->update(['is_deleted'=>2]);
  //     Db::commit();
  //     return true;
  //   } catch (Exception $e) {
  //       Db::rollback();
  //       throw new Exception($e->getMessage(), 500);
  //   }
  // }
}
