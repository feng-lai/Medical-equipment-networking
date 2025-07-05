<?php

namespace app\common\validate;

use think\Validate;

/**
 * 区域管理-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Area extends Validate
{
  protected $rule = [
    'name' => 'require',
  ];

  protected $field = [
    'name' => '名称',
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['name'],
    'edit' => [],
  ];
}
