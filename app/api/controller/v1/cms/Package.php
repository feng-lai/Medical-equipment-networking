<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\PackageLogic;

/**
 * 安装包管理-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class Package extends Api
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
      'keyword_search',
      'type'
    ]);
    $result = PackageLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $result = PackageLogic::cmsDetail($id,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

   public function save()
   {
     $request = $this->selectParam([
       'file',
       'v',
       'type'=>1
     ]);
     $this->check($request, "Package.save");
     $result = PackageLogic::cmsAdd($request,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }

  public function update($id)
  {
    $request = $this->selectParam([
      'file',
      'v',
      'disabled'
    ]);
    $request['uuid'] = $id;
    $result = PackageLogic::cmsEdit($request,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

   public function delete($id)
   {
     $result = PackageLogic::cmsDelete($id,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }
}
