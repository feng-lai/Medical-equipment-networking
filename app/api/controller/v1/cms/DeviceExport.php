<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use app\api\model\Device;
use app\common\tools\AliOss;
use think\Exception;

/**
 * 设备管理导出-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class DeviceExport extends Api
{
  public $restMethodList = 'get';


  public function _initialize()
  {
    parent::_initialize();
    $this->userInfo = $this->cmsValidateToken();
  }

  public function index()
  {
    $request = $this->selectParam([
      'page_size'=>10,
      'page_index'=>1,
      'sim',
      'area_uuid',
      'hospital',
      'type',
      'state',
      'status'
    ]);
    $map = [];
    if($request['area_uuid']) $map['area_uuid'] = ['=',$request['area_uuid']];
    if($request['sim']) $map['sim'] = ['=',$request['sim']];
    if($request['hospital']) $map['hospital'] = ['=',$request['hospital']];
    if($request['status']) $map['status'] = ['=',$request['status']];
    if($request['state']) $map['state'] = ['=',$request['state']];
    if($request['type']) $map['type'] = ['=',$request['type']];
    $sim = get_sim($this->userInfo['uuid']);
    if($sim) $map['sim'] = ['in',$sim];
    $result = Device::build()
      ->where($map)
      ->order('status','desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    foreach($result as $v){
      $v->v = $v->top_v.'/'.$v->btm_v;
    }
    $data = [];
    if($request['state'] && $request['state'] == 1){
      $data[] = ['设备SIM号', '设备型号', '创建时间'];
      foreach ($result as $k => $v) {
        $tmp = [
          $v->sim,
          $v->type,
          $v->create_time
        ];

        foreach ($tmp as $tmp_k => $tmp_v) {
          $tmp[$tmp_k] = $tmp_v.'';
        }
        $data[] = $tmp;
      }
    }
    if($request['state'] && $request['state'] == 2){
      $data[] = ['设备SIM号', '设备型号', '归属医院','上/下位机版本','设备当前状态','最后心跳时间'];
      foreach ($result as $k => $v) {
        $text = '在线';
        //设备当前状态 1=次活跃 2=活跃 3=在线 4=离线 5=故障
        switch ($v->status){
          case 1:
            $text = '次活跃';
            break;
          case 2:
            $text = '活跃';
            break;
          case 3:
            $text = '在线';
            break;
          case 4:
            $text = '离线';
            break;
          case 5:
            $text = '故障';
            break;
        }
        $tmp = [
          $v->sim,
          $v->type,
          $v->hospital,
          $v->v,
          $text,
          $v->heart_time
        ];

        foreach ($tmp as $tmp_k => $tmp_v) {
          $tmp[$tmp_k] = $tmp_v.'';
        }
        $data[] = $tmp;
      }
    }
    if($request['state'] && $request['state'] == 3){
      $data[] = ['设备SIM号', '设备型号', '归属医院','上/下位机版本','封禁时间','最后心跳时间'];
      foreach ($result as $k => $v) {
        $tmp = [
          $v->sim,
          $v->type,
          $v->hospital,
          $v->v,
          $v->disabled_time,
          $v->heart_time
        ];

        foreach ($tmp as $tmp_k => $tmp_v) {
          $tmp[$tmp_k] = $tmp_v.'';
        }
        $data[] = $tmp;
      }
    }

    try{
      $excel = new \PHPExcel();
      $excel_sheet = $excel->getActiveSheet();
      $excel_sheet->fromArray($data);
      $excel_writer = \PHPExcel_IOFactory::createWriter($excel,'Excel2007');

      $file_name = '设备列表.xlsx';
      $file_path = ROOT_PATH .$file_name;
      $excel_writer->save($file_path);

      if (!file_exists($file_path)) {
        throw new \Exception("Excel生成失败");
      }
      //$result = uploadFileExcel($file_name,$file_path,'match_service/excel/');
      $oss = new AliOss();
      $oss->uploadOss($file_path, 'match_service/excel/'.$file_name);
      unlink($file_path);
      $this->render(200, ['result' => 'match_service/excel/'.$file_name]);
    } catch (\Exception $e) {
      unlink($file_path);
      throw new Exception($e->getMessage(), 500);
    }
  }
}
