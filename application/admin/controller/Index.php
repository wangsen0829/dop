<?php

namespace app\admin\controller;

use app\admin\model\AdminLog;
use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Session;
use think\Validate;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }

    /**
     * 后台首页
     */
    public function index()
    {
        $cookieArr = ['adminskin' => "/^skin\-([a-z\-]+)\$/i", 'multiplenav' => "/^(0|1)\$/", 'multipletab' => "/^(0|1)\$/", 'show_submenu' => "/^(0|1)\$/"];
        foreach ($cookieArr as $key => $regex) {
            $cookieValue = $this->request->cookie($key);
            if (!is_null($cookieValue) && preg_match($regex, $cookieValue)) {
                config('fastadmin.' . $key, $cookieValue);
            }
        }
        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general'   => ['new', 'purple'],
        ], $this->view->site['fixedpage']);
        $action = $this->request->request('action');
        if ($this->request->isPost()) {
            if ($action == 'refreshmenu') {
                $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
            }
        }
        $this->assignconfig('cookie', ['prefix' => config('cookie.prefix')]);
        $this->view->assign('menulist', $menulist);
        $this->view->assign('navlist', $navlist);
        $this->view->assign('fixedmenu', $fixedmenu);
        $this->view->assign('referermenu', $referermenu);
        $this->view->assign('title', __('Home'));
        return $this->view->fetch();
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            $this->success(__("You've logged in, do not login again"), $url);
        }
        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle(__('Login'));
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true) {
                Hook::listen("admin_login_after", $this->request);
                $this->success(__('Login successful'), $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Username or password is incorrect');
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            Session::delete("referer");
            $this->redirect($url);
        }
        $background = Config::get('fastadmin.login_background');
        $background = $background ? (stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background) : '';
        $this->view->assign('background', $background);
        $this->view->assign('title', __('Login'));
        Hook::listen("admin_login_init", $this->request);
        return $this->view->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        if ($this->request->isPost()) {
            $this->auth->logout();
            Hook::listen("admin_logout_after", $this->request);
            $this->success(__('Logout successful'), 'index/login');
        }
        $html = "<form id='logout_submit' name='logout_submit' action='' method='post'>" . token() . "<input type='submit' value='ok' style='display:none;'></form>";
        $html .= "<script>document.forms['logout_submit'].submit();</script>";

        return $html;
    }

    public function word(){
//        $params = input();
        $params = [
            'uid'=>78
        ];
        $uid = $params['uid'];
        //模板的路径，word的版本最好是docx，要不然可能会读取不了，根据自己的模板位置调整
        $path = 'uploads/word/surrogacy.docx';
        $user = \app\admin\model\User::find($params['uid']);
//        dump($user);die;
        //生成word路径，根据自己的目录调整
        $time = date("Ymd",time());
        $name = $user['first_name'].$user['last_name'].'-'.$params['uid'];

        $filePath='uploads/word/'.$time.'/';

        if (!file_exists($filePath)){
            mkdir($filePath,0775);
        }
        $filePath= $filePath.$name.'.'.'docx';

        //声明一个模板对象、读取模板
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($path);

        $surrogate_photo = \app\admin\model\SurrogatePhoto::where('uid',$params['uid'])->value('images');
        if($surrogate_photo){
            $surrogate_photo = explode(',',$surrogate_photo);
            $img_count = count($surrogate_photo);
            $templateProcessor->cloneBlock('block_name', $img_count, true, true);
            for($i=0;$i<$img_count;$i++){
                $img = $surrogate_photo[$i];
                $templateProcessor->setImageValue("photo#".($i+1),[
                    'path' => 'http://vip.dopusa.com'.$img,
                    'width' => '600',
                    'height' => '600',
                    'marginTop'=> 20,
                    'marginLeft'=> 50,
                ]);
            }
        }
        else{
            $templateProcessor->cloneBlock('block_name', 0, true, true);
        }

        $pre_screen = \app\admin\model\PreScreen::where('uid',$params['uid'])->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;
        if ($pre_screen){
            $templateProcessor->cloneBlock('pre_screen', 1, true, false);

            $height = round(($pre_screen['height_ft']*12*2.36) + ($pre_screen['height_in']*2.36)).'cm';
            $kg = round($pre_screen['weight']*0.45).'kg';
            $templateProcessor->setValue('first_name',$pre_screen['first_name']);
            $templateProcessor->setValue('birthday_time',date('Y-m-d',$pre_screen['birthday_time']));
            $templateProcessor->setValue('city',$pre_screen['city']);
            $templateProcessor->setValue('state',$pre_screen['state']);
            $templateProcessor->setValue('age',$pre_screen['age']);
            $templateProcessor->setValue('height_ft',$pre_screen['height_ft']);
            $templateProcessor->setValue('height_in',$pre_screen['height_in']);
            $templateProcessor->setValue('height',$height);
            $templateProcessor->setValue('weight',$pre_screen['weight']);
            $templateProcessor->setValue('marital_status',($pre_screen['marital_status']== 1?'Yes':'No'));
            $templateProcessor->setValue('kg',$kg);
        }else{
            $templateProcessor->cloneBlock('pre_screen', 0, true, true);
        }

        $personal_info = \app\admin\model\PersonalInfo::where('uid',$params['uid'])->find();
        $personal_info = $personal_info?$personal_info->toArray():$personal_info;
        if ($personal_info){
            $templateProcessor->cloneBlock('personal', 1, true, false);
            $templateProcessor->setValue('highest_education',$personal_info?$personal_info['highest_education']:'');
            $templateProcessor->setValue('religion',$personal_info?$personal_info['religion']:'');
            $templateProcessor->setValue('race',$personal_info?$personal_info['race']:'');
            $templateProcessor->setValue('partner_occupation',$personal_info?$personal_info['partner_occupation']:'');
        }else{
            $templateProcessor->cloneBlock('personal', 0, true, true);
        }


        $obstertric_history = \app\admin\model\ObstetricHistory::where('uid',$uid)->find();
        $obstertric_history = $obstertric_history?$obstertric_history->toArray():$obstertric_history;
        if ($obstertric_history){
            $templateProcessor->cloneBlock('ob', 1, true, false);
            $surrogate_baby = \app\admin\model\SurrogateBaby::where('uid',$uid)->select();
            if ($surrogate_baby){
                $templateProcessor->cloneBlock('baby', 1, true, false);
                $surrogate_baby = $surrogate_baby?$surrogate_baby:'';
                $baby_count = count($surrogate_baby);
                $templateProcessor->cloneRow('ob_id', $baby_count);
                for($i=0;$i<$baby_count;$i++){
                    $templateProcessor->setValue("ob_id#".($i+1),$i+1);
                    $templateProcessor->setValue("ob_date#".($i+1),date('Y-m-d',$surrogate_baby[$i]['birthday_time']));
                    $templateProcessor->setValue("fetal_age#".($i+1),$surrogate_baby[$i]['fetal_age']);
                    $templateProcessor->setValue("pregnancy_type#".($i+1),$surrogate_baby[$i]['pregnancy_type']?'c-setion':'vaginal');
                    $templateProcessor->setValue("is_surrogacy_pregnancy#".($i+1),$surrogate_baby[$i]['is_surrogacy_pregnancy']==1?'Yes':'No');
                }

                $templateProcessor->cloneBlock('b_artificial', 1, true, false);
                if ($obstertric_history['is_artificial_abortion']==1){
                    $templateProcessor->setValue("is_artificial_abortion",'Yes');
                    $templateProcessor->setValue("artificial_abortion_content",$obstertric_history['artificial_abortion_content']);
                }else{
                    $templateProcessor->cloneBlock('b_artificial', 0, true, true);
                    $templateProcessor->setValue("is_artificial_abortion",'No');
                }
                $templateProcessor->cloneBlock('b_spontaneous', 1, true, false);
                if ($obstertric_history['is_spontaneous_abortion']==1){
                    $templateProcessor->setValue("is_spontaneous_abortion",'Yes');
                    $templateProcessor->setValue("spontaneous_abortion_content",$obstertric_history['spontaneous_abortion_content']);
                }else{
                    $templateProcessor->cloneBlock('b_spontaneous', 0, true, true);
                    $templateProcessor->setValue("is_spontaneous_abortion",'No');
                }

            }else{
                $templateProcessor->cloneBlock('baby', 0, true, true);
            }
        }else{
            $templateProcessor->cloneBlock('ob', 0, true, false);
        }

        //medical information
        $medical_information = \app\admin\model\MedicalInformation::where('uid',$uid)->find();
        $medical_information = $medical_information?$medical_information->toArray():$medical_information;
        if ($medical_information){
            $templateProcessor->cloneBlock('medical', 1, true, false);
            $templateProcessor->setValue("is_smoke",$medical_information['is_smoke']==1?'Yes':'NO');
            $templateProcessor->setValue("smoke_content",$medical_information['is_smoke']==1?$medical_information['smoke_content']:'');
            $templateProcessor->setValue("is_alcoholic_beverages",$medical_information['is_alcoholic_beverages']==1?'Yes':'NO');
            $templateProcessor->setValue("alcoholic_content",$medical_information['is_alcoholic_beverages']==1?$medical_information['alcoholic_content']:'');
            $templateProcessor->setValue("is_take_any_medicine",$medical_information['is_take_any_medicine']==1?'Yes':'NO');
            $templateProcessor->setValue("any_medicine_content",$medical_information['is_take_any_medicine']==1?$medical_information['any_medicine_content']:'');
            $templateProcessor->setValue("is_allergic_medication",$medical_information['is_allergic_medication']==1?'Yes':'NO');
            $templateProcessor->setValue("allergic_medication_content",$medical_information['is_allergic_medication']==1?$medical_information['allergic_medication_content']:'');
            $templateProcessor->setValue("is_hospitalized",$medical_information['is_hospitalized']==1?'Yes':'NO');
            $templateProcessor->setValue("hospitalized_content",$medical_information['is_hospitalized']==1?$medical_information['hospitalized_content']:'');
        }else{
            $templateProcessor->cloneBlock('medical', 0, true, false);
        }

        //about_surrogacy
        $about_surrogacy = \app\admin\model\AboutSurrogacy::where('uid',$uid)->find();
        $about_surrogacy = $about_surrogacy?$about_surrogacy->toArray():$about_surrogacy;
        if ($about_surrogacy){
            $templateProcessor->cloneBlock('about', 1, true, false);
            $templateProcessor->setValue("is_surrogate",$about_surrogacy['is_surrogate']==1?'Yes':'NO');
            $templateProcessor->setValue("begin_surrogate",$about_surrogacy['begin_surrogate']);
            $templateProcessor->setValue("embryo_transfer_number",$about_surrogacy['embryo_transfer_number']);
            $templateProcessor->setValue("is_conceive_twins",$about_surrogacy['is_conceive_twins']==1?'Yes':'NO');
            $templateProcessor->setValue("is_fetal_reduction",$about_surrogacy['is_fetal_reduction']==1?'Yes':'NO');
            $templateProcessor->setValue("is_induced_abortion",$about_surrogacy['is_induced_abortion']==1?'Yes':'NO');
            $templateProcessor->setValue("is_cvs",$about_surrogacy['is_cvs']==1?'Yes':'NO');

            $templateProcessor->setValue("rwp_content",$about_surrogacy['rwp_content']);
            $templateProcessor->setValue("rwp_content_two",$about_surrogacy['rwp_content_two']);
            $templateProcessor->setValue("is_sexual_life",$about_surrogacy['is_sexual_life']==1?'Yes':'NO');
            $templateProcessor->setValue("family_content",$about_surrogacy['family_content']);

        }else{
            $templateProcessor->cloneBlock('about', 0, true, false);
        }

        $other_information = \app\admin\model\OtherInformation::where('uid',$uid)->find();
        $other_information = $other_information?$other_information->toArray():$other_information;
        if ($other_information){
            $templateProcessor->cloneBlock('other', 1, true, false);
            $templateProcessor->setValue("hobby",$other_information['hobby']);
            $templateProcessor->setValue("life_geyan",$other_information['life_geyan']);
            $templateProcessor->setValue("ecybs",$other_information['ecybs']);
            $templateProcessor->setValue("living",$other_information['living']);
        }else{
            $templateProcessor->cloneBlock('other', 0, true, false);
        }
        


        //生成新的word
        $templateProcessor->saveAs($filePath);
        return $filePath;
        if(file_exists($filePath))
        {

            return json(['code'=>1,'data'=>$filePath,'msg'=>'success']);
        }
        else
        {
            return json(['code'=>2,'data'=>$filePath,'msg'=>'fail']);

        }
    }

//public function word(){
//
//    $phpWord = new \PhpOffice\PhpWord\PhpWord();
//
//    $section = $phpWord->addSection();
//
//    //图片
//
//    $section->addTextBreak();
//
//    $section->addImage('http://vip.dopusa.com/uploads/logo.png',[
//        'align'=>'center'
//    ]);
//
//
//
//    //添加文字内容
//    $fontStyle = [
//        'name' => 'Microsoft Yahei UI',
//        'size' => 20,
//        'color' => '#000',
//        'bold' => true,
//        'Underline'=>'Underline',
//        'align'=>'center'
//
//    ];
//
//    $styleCell = ['align'=>'center'];
//    $textrun = $section->addTextRun();
//    $textrun->addText('Surrogate Profile',$fontStyle,$styleCell);
//
//
//    //增加一页
//    $section = $phpWord->addSection();
//    $section->addText('新的一页.');
//
//    //表格
//    $header = array('size' => 16, 'bold' => true);
//
//    $rows = 10;
//    $cols = 5;
//    $section->addText('Basic table', $header);
//
//    $table = $section->addTable();
//    for ($r = 1; $r <= 8; $r++) {
//        $table->addRow();
//        for ($c = 1; $c <= 5; $c++) {
//            $table->addCell(1750)->addText("Row {$r}, Cell {$c}");
//        }
//    }
//
//    //保存
//
//    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
//    $file = 'uploads/word/hellwoeba.docx';
//    $objWriter->save($file);
//    return $file;
//}
public function test(){
//    ini_set('date.timezone','America/New_York');
        date_default_timezone_set('America/New_York');
        dump(date('Y-m-d h:i:s',time()));
}
}
