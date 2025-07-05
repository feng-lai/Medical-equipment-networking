<?php

namespace app\common\validate;

use think\Validate;

/**
 * 试剂更换记录统计-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class RegentChangeStatistics extends Validate
{
  protected $rule = [
    'start_time' => 'require',
    'end_time'=>'require'
  ];

  protected $field = [
    'start_time' => '开始时间',
    'end_time'=>'结束时间'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['start_time','end_time'],
    'edit' => [],
  ];
}
