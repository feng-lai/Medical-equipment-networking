<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Reagent;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 样本测试-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ReagentTestLogic
{
  static public function cmsList($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    if($request['type']) $map['type'] = ['=',$request['type']];
    $request['start_time']?$map['test_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    //$result = Reagent::build()->field('DATE(`create_time`) as order_time,SUM(`sum`) as total')->where($map)->order('create_time','desc')->group('DATE(`create_time`)')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $result = Reagent::build()->where($map)->order('test_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']])->toArray();
    foreach($result['data'] as $k=>$v){
      if(!$v['res_val'] && !$v['blood_sugar'] && !$v['lactate']){
        //unset($result['data'][$k]);
      }
    }
    $result['data'] = array_values($result['data']);
    AdminLog::build()->add($userInfo['uuid'], '样本测试结果管理','查询列表');
    return $result;
  }

  static public function cmsDetail($request)
  {
    $map = [];
    $request['sim']?$map['sim'] = $request['sim']:'';
    $result = Reagent::build()->where($map)->group('type')->column('type');
    $result = array_values(array_filter($result,'strlen'));
    return $result;
  }
  static public function export($request,$userInfo)
  {
    $map = [];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    if($request['type']) $map['type'] = ['=',$request['type']];
    $request['start_time']?$map['test_time'] = ['between time',[$request['start_time'],$request['end_time'].' 23:59:59']]:'';
    $result = Reagent::build()->where($map)->order('test_time','desc')->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['测试时间', '序号', '类型','结果值','血糖值','乳酸值','总面积','参数二（暂定名字）'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->test_time,
        $v->number,
        $v->type,
        $v->res_val,
        $v->blood_sugar,
        $v->lactate,
        $v->area,
        $v->par2
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

      $file_name = '样本测试结果.xlsx';
      $file_path = ROOT_PATH .$file_name;
      $excel_writer->save($file_path);

      if (!file_exists($file_path)) {
        throw new \Exception("Excel生成失败");
      }
      //$result = uploadFileExcel($file_name,$file_path,'match_service/excel/');
      $oss = new AliOss();
      $oss->uploadOss($file_path, 'match_service/excel/'.$file_name);
      unlink($file_path);
      return 'match_service/excel/'.$file_name;
    } catch (\Exception $e) {
      unlink($file_path);
      throw new Exception($e->getMessage(), 500);
    }
  }


}
