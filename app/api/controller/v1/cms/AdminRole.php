<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\AdminRoleLogic;

/**
 * 后台角色-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AdminRole extends Api
{
  public $restMethodList = 'get|put|post|delete';


  public function _initialize()
  {
    parent::_initialize();
    $this->userInfo = $this->cmsValidateToken();
  }

  public function index()
  {
    $result = AdminRoleLogic::cmsList($this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $result = AdminRoleLogic::cmsDetail($id,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

   public function save()
   {
     $request = $this->selectParam([
       'name',
       'menus',
       'serial_number'=>0
     ]);
     $this->check($request, "AdminRole.save");
     $result = AdminRoleLogic::cmsAdd($request,$this->userInfo);
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
    $result = AdminRoleLogic::cmsEdit($request,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

   public function delete($id)
   {
     $result = AdminRoleLogic::cmsDelete($id,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }
}
