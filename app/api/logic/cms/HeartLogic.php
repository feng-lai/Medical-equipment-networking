<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Heart;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 心跳记录-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class HeartLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = Heart::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '心跳记录管理','查询列表');
    return $result;
  }

  static public function cmsDetail($request,$userInfo)
  {
    //导出
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = Heart::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['柱温', '柱压', '校准参数','上位机软件版本号','下位机软件版本号','心跳时间'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->com_tem,
        $v->com_pre,
        $v->cal_par,
        $v->top_v,
        $v->btm_v,
        $v->create_time
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

      $file_name = '心跳记录.xlsx';
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
