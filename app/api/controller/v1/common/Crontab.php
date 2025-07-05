<?php

namespace app\api\controller\v1\common;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use app\api\controller\Api;
use app\api\logic\common\ClearUserIntegralLogic;
use app\api\logic\common\CreateRankingLogic;
use app\api\logic\common\OrderPayStateLogic;
use app\api\logic\common\OrderReceiveStateLogic;
use app\api\logic\common\OrderStatisticsLogic;
use app\api\logic\common\QuestionnaireLogic;
use app\api\logic\common\ResetCountersignLogic;
use Exception;

class Crontab extends Api
{

    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|options';

    /**
     * @param string $type
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws \think\Exception
     */
    public function index($type = "")
    {
        switch ($type) {
            case 'ResetCountersign':
                // 每月重置会员补签的次数
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=ResetCountersign
                // 每月1日0时0分0秒执行一次
                ResetCountersignLogic::sync();
                break;
            case 'OrderPayState':
                // 订单支付状态检查&积分商品状态&问卷调查
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=OrderPayState
                // 每秒执行一次
                OrderPayStateLogic::sync();
                break;
            case 'OrderReceiveState':
                // 订单收货状态检查
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=OrderReceiveState
                // 每秒执行一次
                OrderReceiveStateLogic::sync();
                break;
            case 'ClearUserIntegral':
                // 清空用户积分&拍照活动状态更新
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=ClearUserIntegral
                // 每日0时0分0秒执行一次
                ClearUserIntegralLogic::sync();
                break;
            case 'CreateRanking':
                // 生成排行榜
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=CreateRanking
                // 每日0时0分0秒执行一次
                CreateRankingLogic::sync();
                break;
            case 'OrderStatistics':
                // 分销订单日统计 
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=OrderStatistics
                // 每日0时0分0秒执行一次
                OrderStatisticsLogic::sync();
                break;
            case 'Questionnaire':
                // 问卷调查定时器（废弃）
                // http://sw-alcohol-api.gymooit.cn/v1/common/Crontab?type=Questionnaire
                // 每日0时0分0秒执行一次
                QuestionnaireLogic::sync();
                break;
        }
    }

    function getCurl($url)
    {
        try {
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($curlHandle);
            curl_close($curlHandle);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }
}
