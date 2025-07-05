<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\LoginLogic;

/**
 * 后台登陆-控制器
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Login extends Api
{
  public $restMethodList = 'get|post|put|delete';


  public function _initialize()
  {
    parent::_initialize();
  }

  public function save()
  {
    $request = $this->selectParam([
      'uname', // 账号
      'password', // 密码
      'code'
    ]);
    $this->check($request, "Login.save");
    $result = LoginLogic::cmsAdd($request, $this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

  // public function index(){
  //   $request = $this->selectParam([
  //     'page_index'=>1,      // 当前页码
  //     'page_size'=>10,      // 每页条目数
  //     'keyword_search'=>'', // 关键词
  //     'start_time'=>'',     // 开始时间
  //     'end_time'=>''        // 结束时间
  //   ]);
  //   $result = loginByPasswordLogic::cmsList($request,$this->userInfo);
  //   $this->render(200,['result' => $result]);
  // }

  // public function read($id){
  //   $result = loginByPasswordLogic::cmsDetail($id,$this->userInfo);
  //   $this->render(200,['result' => $result]);
  // }

  // public function update($id)
  // {
  //   $request = $this->selectParam([]);
  //   $request['uuid'] = $id;
  //   $result = loginByPasswordLogic::cmsEdit($request, $this->userInfo);
  //   if (isset($result['msg'])) {
  //     $this->returnmsg(400, [], [], '', '', $result['msg']);
  //   } else {
  //     $this->render(200, ['result' => $result]);
  //   }
  // }

  // public function delete($id)
  // {
  //   $result = loginByPasswordLogic::cmsDelete($id, $this->userInfo);
  //   if (isset($result['msg'])) {
  //     $this->returnmsg(400, [], [], '', '', $result['msg']);
  //   } else {
  //     $this->render(200, ['result' => $result]);
  //   }
  // }
}
