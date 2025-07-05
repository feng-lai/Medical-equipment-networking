<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\CheckCalibration;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 质检-校准-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class CheckCalibrationLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = CheckCalibration::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    AdminLog::build()->add($userInfo['uuid'], '质检-校准管理','查询列表');
    return $result;
  }

  static public function cmsDetail($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['create_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = CheckCalibration::build()->where($map)->order('create_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['日期', '类型','数据来源','批号','校准高值低值','质控高值低值'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->test_time,
        $v->kind == 1?'质检':'校准',
        $v->origin,
        $v->b_number.' ',
        '('.($v->ca_val?$v->ca_val:'-').'),'.'('.($v->ca_t_cv?$v->ca_t_cv:'-').','.($v->ca_b_cv?$v->ca_b_cv:'-').')'.'('.($v->ca_res?$v->ca_res:'-').')',
        '('.($v->ctr_val?$v->ctr_val:'-').'),'.'('.($v->qc_res_b?$v->qc_res_b:'-').','.($v->qc_res_t?$v->qc_res_t:'-').')'
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

      $file_name = '质检-校准.xlsx';
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
