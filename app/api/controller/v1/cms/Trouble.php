<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\TroubleLogic;

/**
 * 故障记录-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class Trouble extends Api
{
  public $restMethodList = 'get|put|post|delete';


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
      'start_time',
      'end_time',
      'code',
      'is_deleted'
    ]);
    $result = TroubleLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $request = $this->selectParam([
      'page_size'=>10,
      'page_index'=>1,
      'sim',
      'start_time',
      'end_time',
      'code'
    ]);
    $result = TroubleLogic::cmsDetail($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

}
