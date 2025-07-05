<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Reagent;
use think\Exception;
use think\Db;

/**
 * 试剂余量-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ReagentLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $result = Reagent::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '试剂余量管理','查询列表');
    return $result;
  }

  static public function cmsDetail($id,$userInfo)
  {

    $data =  Reagent::build()
      ->where('uuid', $id)
      ->field('*')
      ->find();
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    AdminLog::build()->add($userInfo['uuid'], '试剂余量管理','查看详情：'.$data->name);
    return $data;
  }


}
