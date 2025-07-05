<?php

namespace app\common\validate;

use think\Validate;

/**
 * 试剂更换记录-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class RegentChange extends Validate
{
  protected $rule = [
    'sim' => 'require',
    'type'=>'require'
  ];

  protected $field = [
    'sim' => 'SIM号',
    'type'=>'试剂类型'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['sim','type'],
    'edit' => [],
  ];
}
