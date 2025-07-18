<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用行为扩展定义文件
return [
    // 应用初始化
    'app_init'     => [],
    // 应用开始
    'app_begin'    => [],
    // 模块初始化
    'module_init'  => [],
    // 操作开始执行
    'action_begin' => [],
    // 视图内容过滤
    'view_filter'  => [],
    // 日志写入
    'log_write'    => [],
    // 应用结束
    'app_end'      => [],

    'saveBalanceRecord' => [
        \app\api\behavior\saveBalanceRecord::class
    ],

    'saveCoinRecord' => [
        \app\api\behavior\saveCoinRecord::class
    ],

    'completeTrade' => [
        \app\api\behavior\completeTrade::class
    ],

    'orderComplete' => [
        \app\api\behavior\orderComplete::class
    ],

    'auctionOrderComplete' => [
        \app\api\behavior\auctionOrderComplete::class
    ],

    'savePostIdentifyOrder' => [
        \app\api\behavior\savePostIdentifyOrder::class
    ],
    'completeTransfer' => [
        \app\api\behavior\completeTransfer::class
    ],
    'timer' => [
        \app\api\behavior\timer::class
    ],

    'saveMerchantBalanceRecord' => [
        \app\api\behavior\saveMerchantBalanceRecord::class
    ],
    'saveMerchantBondRecord' => [
        \app\api\behavior\saveMerchantBondRecord::class
    ],
    'orderRefund' => [
        \app\api\behavior\orderRefund::class
    ],
];
