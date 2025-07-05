<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\HeartPackageLogic;

/**
 * 心跳包-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class HeartPackage extends Api
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
    ]);
    $result = HeartPackageLogic::cmsList($request,$this->userInfo);
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
    ]);
    $result = HeartPackageLogic::cmsDetail($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function delete($id)
  {
    $result = HeartPackageLogic::cmsDelete($id,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

}
