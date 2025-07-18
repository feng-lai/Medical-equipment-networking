<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]
use think\Container;

if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

ini_set('opcache.revalidate_freq',0);
// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');
// 定义静态资源目录 或 URL
define('STATIC_PATH', '/static/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
