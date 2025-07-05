<?php

namespace app\common\validate;

use think\Validate;

/**
 * 用户列表-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 19:38
 */
class User extends Validate
{
  protected $rule = [
    'gender' => 'number',
  ];

  protected $field = [
    'nickname' => '昵称',
    'mobile' => '手机号',
    'avatar' => '头像',
    'gender' => '性别',
    'birthday' => '生日',
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => [],
    'edit' => []
  ];
}
