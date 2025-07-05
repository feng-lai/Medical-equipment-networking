<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\ReagentDay;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 样本测试统计-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ReagentTestStatisticsLogic
{
  static public function cmsList($request,$userInfo)
  {

    $date_time = json_decode($request['date_time']);
    $type = ['测试样本量','血糖样本量','乳酸样本量'];
    $map = [];
    $request['sim']?$map['sim'] = $request['sim']:'';
    $is = explode('-',$date_time[0]);
    if(count($is) == 1){
      $data = ReagentDay::build()->field('SUM(blood_sugar) as blood_sugar,SUM(lactate) as lactate,DATE_FORMAT(`test_time`, "%Y") as test_time,SUM(`sum`) as `sum`')->where($map)->where('DATE_FORMAT(`test_time`, "%Y")','in',$date_time)->group('DATE_FORMAT(`test_time`, "%Y")')->select();
    }else{
      $map['test_time'] = ['in',$date_time];
      $data = ReagentDay::build()->field('SUM(blood_sugar) as blood_sugar,SUM(lactate) as lactate,test_time,SUM(`sum`) as `sum`')->where($map)->group('test_time')->select();
    }
    $res = [];
    foreach ($type as $v) {
      $arr = [];
      foreach ($date_time as $val) {
        $info = '';
        foreach($data as $vol){
          if($vol->test_time == $val){
            if($v == '测试样本量'){
              $info = $vol->sum;
            }elseif ($v == '血糖样本量'){
              $info = $vol->blood_sugar;
            }elseif ($v == '乳酸样本量'){
              $info = $vol->lactate;
            }
          }
        }
        $arr[] = [
          'date'=>$val,
          'contains'=>$info,
        ];
      }
      $res[] = [
        'text'=>$v,
        'info'=>$arr
      ];
    }
    return $res;
  }
  static public function table($request){
    $map['test_time'] = ['<>','0000-00-00 00:00:00'];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['test_time'] = ['between time',[$request['start_time'],$request['end_time']]]:'';
    $result = ReagentDay::build()
      ->field('test_time,origin,sim,SUM(blood_sugar) as blood_sugar,SUM(`sum`) as `sum`,SUM(lactate) as lactate')
      ->where($map)
      ->group('test_time')
      ->order('test_time','desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    return $result;
  }

  static public function table_export($request){
    $map['test_time'] = ['<>','0000-00-00 00:00:00'];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    $request['start_time']?$map['test_time'] = ['between time',[$request['start_time'],$request['end_time']]]:'';
    $result = ReagentDay::build()
      ->field('test_time,origin,sim,blood_sugar,sum,lactate')
      ->where($map)
      ->order('test_time','desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['日期', '测试样本总量', '血糖值','乳糖值','来源'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->test_time,
        $v->sum,
        $v->blood_sugar,
        $v->lactate,
        $v->origin,
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
