<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Trouble;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 故障记录-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class TroubleLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    if($request['code']) $map['code'] = ['=',$request['code']];
    if($request['is_deleted']) $map['is_deleted'] = ['=',$request['is_deleted']];
    $request['start_time']?$map['trouble_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = Trouble::build()->where($map)->order('trouble_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '故障记录管理','查询列表');
    return $result;
  }

  static public function cmsDetail($request,$userInfo)
  {
    //导出
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    if($request['code']) $map['code'] = ['=',$request['code']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = Trouble::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['故障发生时间', '故障代码', '故障描述','上位机软件版本号','下位机软件版本号'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->trouble_time,
        $v->code,
        $v->dsc,
        $v->top_v,
        $v->btm_v
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

      $file_name = '故障记录.xlsx';
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
