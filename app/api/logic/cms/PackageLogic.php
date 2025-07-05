<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Package;
use think\Exception;
use think\Db;

/**
 * 安装包管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class PackageLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['is_deleted'] = ['=',1];
    if($request['disabled']) $map['disabled'] = ['=',$request['disabled']];
    if($request['type']) $map['type'] = ['=',$request['type']];
    if($request['keyword_search']) $map['v'] = ['like','%'.$request['keyword_search'].'%'];
    $result = Package::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '安装包管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  Package::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '安装包管理','查看详情：'.$data->uuid);
    return $data;
  }

   static public function cmsAdd($request,$userInfo){
     try {
       if(Package::build()->where(['type'=>$request['type'],'v'=>$request['v'],'is_deleted'=>1])->count()){
          return ['msg'=>'版本已存在'];
       }
       $data = [
         'uuid' => uuid(),
         'file'=>$request['file'],
         'v'=>$request['v'],
         'type'=>$request['type'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       Package::build()->save($data);
       AdminLog::build()->add($userInfo['uuid'], '安装包管理','新增：'.$data['uuid']);
       return $data['uuid'];
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = Package::build()->where('uuid', $request['uuid'])->find();
      if($request['v']){
        if(Package::build()->where('uuid','<>',$request['uuid'])->where('type',$data->type)->where('v',$request['v'])->count()){
          return ['msg'=>'版本已存在'];
        }
        $data->v = $request['v'];
      }
      if($request['file']){
        $data->file = $request['file'];
      }
      if($request['disabled']) $data->disabled = $request['disabled'];
      $data->save();
      AdminLog::build()->add($userInfo['uuid'], '安装包管理','更新：'.$data->uuid);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id,$userInfo){
     try {
       $data = Package::build()->where('uuid',$id)->findOrFail();
       $data->save(['is_deleted'=>2]);
       AdminLog::build()->add($userInfo['uuid'], '安装包管理','删除：'.$data->uuid);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
