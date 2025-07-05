<?php

namespace app\api\controller\v1\common;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\common\WechatLoginLogic;

/**
 * 微信登录-控制器
 * User: Yacon
 * Date: 2022-02-15
 * Time: 10:36
 */
class WechatLogin extends Api
{
  public $restMethodList = 'get|post|put|delete';

  public function save()
  {
    $request = $this->selectParam([
      'type' => 'user', // 终端类型 user=用户端
      'code', // 用户唯一标识
      'user_uuid',
      'mobile',
      'v_code',
      'unionid'
    ]);
    $this->check($request, "WechatLogin.save");
    $result = WechatLoginLogic::commonAdd($request);
    if (isset($result['msg'])) {
      $this->returnmsg(400, $result['unionid'], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }
}
