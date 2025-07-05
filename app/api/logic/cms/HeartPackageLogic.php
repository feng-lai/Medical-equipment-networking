<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\HeartPackage;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 心跳包-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class HeartPackageLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map['is_deleted'] = 1;
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = HeartPackage::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '心跳包管理','查询列表');
    return $result;
  }

  static public function cmsDetail($request,$userInfo)
  {
    $map['is_deleted'] = 1;
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = HeartPackage::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['接受时间', '心跳包ID'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->create_time,
        $v->uuid,
      ];

      foreach ($tmp as $tmp_k => $tmp_v) {
        $tmp[$tmp_k] = $tmp_v.'';
      }
      $data[] = $tmp;
    }

    try{
      $excel = new \PHPExcel();
      $excel_sheet = $excel->getActiveSheet();
      $excel_sheet->fromArray($data);
      $excel_writer = \PHPExcel_IOFactory::createWriter($excel,'Excel2007');

      $file_name = '心跳包.xlsx';
      $file_path = ROOT_PATH .$file_name;
      $excel_writer->save($file_path);

      if (!file_exists($file_path)) {
        throw new \Exception("Excel生成失败");
      }
      //$result = uploadFileExcel($file_name,$file_path,'match_service/excel/');
      $oss = new AliOss();
      $oss->uploadOss($file_path, 'lnh_service/excel/'.$file_name);
      unlink($file_path);
      return 'lnh_service/excel/'.$file_name;
    } catch (\Exception $e) {
      unlink($file_path);
      throw new Exception($e->getMessage(), 500);
    }
  }

  static public function cmsDelete($id,$userInfo){
    try {
      $data = HeartPackage::build()->where('uuid',$id)->findOrFail();
      $data->save(['is_deleted'=>2]);
      AdminLog::build()->add($userInfo['uuid'], '心跳包管理','删除：'.$data->uuid);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }


}
