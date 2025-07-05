<?php

namespace app\common\validate;

use think\Validate;

/**
 * 后台登陆-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Login extends Validate
{
  protected $rule = [
    'uname' => 'require',
    'password' => 'require',
    'code'=>'require'
  ];

  protected $field = [
    'uname' => '账号',
    'password' => '密码',
    'code'=>'验证码'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['uname', 'password','code'],
    'edit' => [],
  ];
}
