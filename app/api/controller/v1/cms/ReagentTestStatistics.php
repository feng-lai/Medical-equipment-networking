<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\ReagentTestStatisticsLogic;

/**
 * 样本测试统计-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ReagentTestStatistics extends Api
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
      'date_time',
      'sim',
    ]);
    $result = ReagentTestStatisticsLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id){

    if($id == 'Export'){
      $request = $this->selectParam([
        'start_time',
        'end_time',
        'sim',
      ]);
      $result = ReagentTestStatisticsLogic::export($request,$this->userInfo);
    }
    if($id == 'Table'){
      $request = $this->selectParam([
        'start_time',
        'end_time',
        'sim',
        'page_size'=>10,
        'page_index'=>1,
      ]);
      $result = ReagentTestStatisticsLogic::table($request,$this->userInfo);
    }
    if($id == 'TableExport'){
      $request = $this->selectParam([
        'start_time',
        'end_time',
        'sim',
        'page_size'=>10,
        'page_index'=>1,
      ]);
      $result = ReagentTestStatisticsLogic::table_export($request,$this->userInfo);
    }
    $this->render(200, ['result' => $result]);
  }


}
