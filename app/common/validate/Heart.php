<?php

namespace app\common\validate;

use think\Validate;

/**
 * 心跳-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Heart extends Validate
{
  protected $rule = [
    'sim' => 'require',
  ];

  protected $field = [
    'sim' => 'SIM号',
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['sim'],
    'edit' => [],
  ];
}
