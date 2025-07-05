<?php

namespace app\api\controller\v1\common;

use app\api\controller\Api;
use app\common\tools\AliOss;

class Upload extends Api
{
    public function save()
    {
        $file = request()->file('file');

        empty($file) ? $file = request()->file('upload') : '';
        empty($file) ? $this->returnmsg(400, [], [], "", "param error", "请传入文件") : '';

        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
        empty($info) ? $this->returnmsg(403, [], [], 'Forbidden', '', $file->getError()) : '';

        $filePath = str_replace('\\', '/', $info->getSaveName());
        //print_r($filePath);exit;
        //$photo = 'lnh_service/' . uuid();
        //$photo = $photo . strrchr($file->getInfo()['name'], '.');

        //$filePath = ROOT_PATH . 'public' . DS . 'upload'. DS . $filePath;
        //try {
            //$oss = new AliOss();
           // $oss->uploadOss($filePath, $photo);
            //@unlink($filePath);
            $this->render(200, ['result' => 'upload/'.$filePath]);
        //} catch (\Exception $e) {
            //unlink($filePath);
            $this->returnmsg(403, [], [], 'Forbidden', '', $e->getMessage());
        //}
    }
}
