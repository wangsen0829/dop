<?php

namespace app\common\library;

use app\common\library\Email;

class Wsemail
{
    /**
     * 发送邮件
     * @return boolean
     */
    public function send_email()
    {
        $email = new Email();

        $res = $email->to('247121925@qq.com')
            ->subject('dop网站邀您注册')
            ->message('正文')
            ->send();
        if ($res !== false ){
            $this->success('发送成功');
        }else{
            $this->error('发送失败',$email->getError());
        }
    }


}
