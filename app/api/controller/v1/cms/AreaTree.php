<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\AreaTreeLogic;

/**
 * 区域树管理-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AreaTree extends Api
{
  public $restMethodList = 'get|put|post|delete';


  public function _initialize()
  {
    parent::_initialize();
    $this->userInfo = $this->cmsValidateToken();
  }

  public function index()
  {
    $request = $this->selectParam(['hide_city']);
    $result = AreaTreeLogic::cmsList($request);
    $this->render(200, ['result' => $result]);
  }
}
