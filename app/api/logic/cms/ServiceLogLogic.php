<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\ServiceLog;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 维修日志-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ServiceLogLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['is_deleted'] = 1;
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = ServiceLog::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '维修日志管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  ServiceLog::build()
      ->where('uuid', $id)
      ->where('is_deleted',1)
      ->field('*')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '维修日志管理','查看详情：'.$data->name);
    return $data;
  }

  static public function cmsAdd($request,$userInfo){
    try {
      $request['uuid'] = uuid();
      ServiceLog::build()->save($request);
      AdminLog::build()->add($userInfo['uuid'], '维修日志管理','新增：'.$request['uuid']);
      return $request['uuid'];
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

  static public function cmsEdit($request,$userInfo)
  {
    try {
      $data = ServiceLog::build()->where('uuid', $request['uuid'])->find();
      $data->save($request);
      AdminLog::build()->add($userInfo['uuid'], '维修日志管理','更新：'.$data->uuid);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

  static public function cmsDelete($id,$userInfo){
    try {
      $data = ServiceLog::build()->where('uuid',$id)->find();
      if(!$data){
        return ['msg'=>'数据不存在'];
      }
      if($data->is_deleted == 2){
        return ['msg'=>'数据已删除'];
      }
      if($data->type == 2){
        return ['msg'=>'钉钉审批的不能删除'];
      }
      $data->save(['is_deleted'=>2]);
      AdminLog::build()->add($userInfo['uuid'], '维修日志管理','删除：'.$data->uuid);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }


}
