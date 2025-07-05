<?php

namespace app\api\controller\v1\common;

use app\api\controller\Api;
use app\api\controller\Sms;
use app\api\model\Captcha;
use think\Exception;

class SendSmsCode extends Api
{
    public $restMethodList = 'post|put';

    /**
     * 获取手机验证码
     */
    public function save()
    {
        $request = $this->selectParam(['mobile']);
        $this->check($request, "SendSmsCode.captcha");
        $smsObj = new Sms();
        $res = $smsObj->send_notice($request['mobile'],['hospital'=>'测试医院','device_type'=>'设备型号','date'=>'2023-10-26','time'=>'14:44:00','error_code'=>'5203','error_des'=>'关机失败','device'=>'设备sim号','name'=>'设备联系人','phone'=>'13672574259','hos_address'=>'医院地址']);
        if ($res) {
            $this->render(200, ['result' => true]);
        }
        $this->returnmsg(403, $data = [], $header = [], $type = "", "Server error", $message = "发送失败");
    }

    /**
     * 验证码校验
     */
    public function update($id)
    {
        $request = $this->selectParam([
          'code',
          'type'=>1
        ]);
        if(!$request['code']){
          throw new Exception('验证码不能为空');
        }
        $request['mobile'] = $id;
        Captcha::build()->captchaCheck($request);
        $this->render(200, ['result' => true]);
    }
}
