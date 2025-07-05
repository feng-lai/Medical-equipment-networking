<?php

namespace app\api\logic\mini;

use think\Exception;
use think\Db;
use app\api\model\Package;

/**
 * 安装包-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class PackageLogic
{


  static public function miniList($request)
  {
    try {
      $map['is_deleted'] = 1;
      $map['disabled'] = 1;
      $request['type']?$map['type'] = $request['type']:'';
      $result = Package::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
      foreach($result as $k=>$v){
        $file = $v->file;
        //$file[0]['url'] = config('alioss.url').$v->file[0]['url'];
        $v->file = $file;
      }
      return $result;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

  static public function miniDetail($id)
  {

    $data =  Package::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->where('disabled','=',1)
      ->find();
    $file = $data->file;
    //$file[0]['url'] = config('alioss.url').$data->file[0]['url'];
    $data->file = $file;
    if(!$data){
      return ['msg'=>'数据不存在'];
    }
    return $data;
  }
}
