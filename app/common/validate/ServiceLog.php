<?php

namespace app\common\validate;

use think\Validate;

/**
 * 维修日志-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class ServiceLog extends Validate
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
