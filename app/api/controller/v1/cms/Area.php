<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\AreaLogic;

/**
 * 区域管理-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class Area extends Api
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
      'disabled',
      'name'
    ]);
    $result = AreaLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $result = AreaLogic::cmsDetail($id,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

   public function save()
   {
     $request = $this->selectParam([
       'name'
     ]);
     $this->check($request, "Area.save");
     $result = AreaLogic::cmsAdd($request,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }

  public function update($id)
  {
    $request = $this->selectParam([]);
    $request['uuid'] = $id;
    unset($request['id']);
    unset($request['version']);
    $result = AreaLogic::cmsEdit($request,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

   public function delete($id)
   {
     $result = AreaLogic::cmsDelete($id,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }
}
