<?php

namespace app\index\controller;
use app\admin\model\RegnancyInformation;
use app\admin\model\SurrogateBaby;
use app\admin\model\FormNumber;
use app\admin\model\PreScreen;
use app\common\controller\Frontend;
use think\Request;
use think\Validate;
use app\common\library\Email;

class Surrogacy extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    public function index()
    {

        return $this->view->fetch();
    }
    public function test(){
        return $this->view->fetch();
    }
    public function fax()
    {

        return $this->view->fetch();
    }

    public function pre_screen(Request $request){

        $uid = input('uid');
        $row = PreScreen::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','pre_screen')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }

        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $model = PreScreen::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] == 1;
            }
            $params['form_complate_number'] = $form_number - $params['form_complate_number'];
            if ($model){
                PreScreen::where('uid',$uid)->update($params);
            }else{
                PreScreen::create($params);
            }
            $this->success('successful','index/user/personal_info');
        }

        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }
    public function hospital(){
        $uid = $this->auth->id;
        $regnancy_information = RegnancyInformation::where('uid',$uid)->find();
        $regnancy_information = $regnancy_information?$regnancy_information:'';

        $surrogate_baby = SurrogateBaby::where('uid',$uid)->select();
        $surrogate_baby = $surrogate_baby?$surrogate_baby:'';
        $this->view->assign("regnancy_information", $regnancy_information);
        $this->view->assign("surrogate_baby", $surrogate_baby);
        return $this->view->fetch();
    }
    public function exl(){
        //模板的路径，word的版本最好是docx，要不然可能会读取不了，根据自己的模板位置调整
        $path = 'uploads/fax/moban.docx';

        //生成word路径，根据自己的目录调整
        $filePath='uploads/fax/2.docx';

        //声明一个模板对象、读取模板
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($path);

        //替换模板中的变量，对应word里的 ${test}
        $test ="这是替换的内容";
        $templateProcessor->setValue('test',$test);//传真

        //生成新的word
        $templateProcessor->saveAs($filePath);
        if(file_exists($filePath))
        {
            return json(['code'=>1,'data'=>$filePath,'msg'=>'success']);
        }
        else
        {
            return json(['code'=>2,'data'=>$filePath,'msg'=>'fail']);

        }
    }

    public function send_email(){
//        $params = input();
        $email = new Email();
        $admin = 'admin';

        $html =$this->html('247121925@qq.com','http://dop.ifcivf.cn/','18811231275','247121925@qq.com',$admin);

        $res = $email->to('247121925@qq.com')
               ->subject('dop网站邀您注册')
               ->message($html)
               ->send();
        if ($res !== false ){
//            $this->success('发送成功');
            return "发送成功";
        }else{
//            $this->error('发送失败',$email->getError());
            return $email->getError();
        }

    }

    public function html($mail,$url,$company_phone,$company_email,$admin){
        return '
         <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Email</title>
    <style>
        .landing-page {
            width: 700px;
            padding: 30px 70px 70px 70px;
            margin: 0 auto;
        }
        .logo {
            padding: 50px 70px 0px 70px;
        }

        .logo img {
            height: 100px;
            margin: 0 auto;
            display: block;
        }

        .content {
            padding-left: 70px;
            padding-right: 70px;

        }
    </style>
</head>
<body >
<div class="landing-page">
    <!--    title-->
    <div class="logo">
        <img src="http://dop-x-ifcivf-x-cn.img.abc188.com/uploads/dop.png" alt="">
    </div>
    <div class="content">

        <h2 class="title">
            亲爱的，
        </h2>
        <p>
            <span>
                感谢您的关注！我们已经在我们的代母申请门户上自动为您创建了一个帐户。
            </span>
            <br>
            <span>
                账号是您的邮箱：'.$mail.'，
            </span>

            <span>
                密码：123456789（请及时更改您的密码）
            </span>

            <br>
            <span>
                您还可以使用以下链接，通过账号和密码登录您的代理申请门户帐户。
            </span>
        </p>
        <a href="http://dop.ifcivf.cn/index/user/login.html" target="_blank">
            <button>
                代母申请门户
            </button>
        </a>
        <p>
            如果您有任何疑问并想与我们联系，您可以直接与我们交谈。
            <br>
            <span>
                电话：'.$company_phone.'
            </span>
            <br>
            <span>
                邮箱：'.$company_email.'
            </span>
        </p>
        <p>
            再次感谢您对我们机构的关注，我们期待为您提供进一步的帮助。感谢您的宝贵时间，希望您度过美好的一天！
        </p>
        <p>
            <span>
                亲切的问候，
            </span>
            <br>
            <span>
                '.$admin.'
            </span>
        </p>
        <p>
            <span>
                办公室：  '.$company_phone.'
            </span>
            <br>
            <span>
                 '.$url.'
            </span>
        </p>


    </div>
</div>
</body>
</html>
        ';
    }
    
}
