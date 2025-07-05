<?php

namespace app\api\logic\cms;

use app\api\model\Captcha;
use think\Exception;
use think\Db;

/**
 * 验证码-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class CaptchaLogic
{
  static public function cmsList()
  {
    $ip = $_SERVER['REMOTE_ADDR'];
    return $result;
  }


}
