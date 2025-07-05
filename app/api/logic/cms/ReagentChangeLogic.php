<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\ReagentChange;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 试剂更换记录-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ReagentChangeLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['change_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = ReagentChange::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '试剂更换记录管理','查询列表');
    return $result;
  }

  static public function cmsDetail($request)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['change_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = ReagentChange::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['日期', '批号', '试剂种类', '更换量', '有效期'];
    foreach ($result as $k => $v) {
      $type = '试剂A';
      switch ($v->type){
        case 2:
          $type = '试剂B';
          break;
        case 3:
          $type = '试剂C';
          break;
        case 4:
          $type = '试剂H';
          break;
        case 5:
          $type = '层柱析';
          break;
      }
      $tmp = [
        date('Y-m-d',strtotime($v->change_time)),
        $v->lot_num,
        $type,
        $v->contains_t,
        $v->validity,
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

      $file_name = '试剂更换记录.xlsx';
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



}
