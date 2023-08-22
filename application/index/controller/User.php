<?php

namespace app\index\controller;
use app\admin\model\DonorCharacter;
use app\admin\model\DonorEducation;
use app\admin\model\DonorEthnicity;
use app\admin\model\DonorMedical;
use app\admin\model\DonorPersonal;
use app\admin\model\DonorPhotos;
use app\admin\model\DonorPreScreen;
use app\common\library\Email;
use addons\wechat\model\WechatCaptcha;
use app\admin\model\AboutSurrogacy;
use app\admin\model\Examine;
use app\admin\model\AdditionalInformation;
use app\admin\model\FormNumber;
use app\admin\model\HealthRecordRelease;
use app\admin\model\MedicalInformation;
use app\admin\model\ObstetricHistory;
use app\admin\model\OtherInformation;
use app\admin\model\PersonalInfo;
use app\admin\model\PreScreen;
use app\admin\model\RegnancyInformation;
use app\admin\model\SurrogateBaby;
use app\admin\model\SurrogatePhoto;
use app\common\controller\Frontend;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\Attachment;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Model;
use think\Session;
use think\Validate;
use fast\Random;
/**
 * 会员中心
 */
class User extends Frontend
{
    protected $layout = 'default';
//    protected $noNeedLogin = ['login', 'register', 'third','forgot_password','pass_email'];
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {

        parent::_initialize();
        $auth = $this->auth;

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'), '/');
        }
        $serviceList = Config::get('site.service');
        $this->view->assign('serviceList', $serviceList);
        //监听注册登录退出的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = input('post.keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_delete_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
    }

    public function number($name='',$type=''){
        $number = FormNumber::where('name',$name)->where('type',$type)->value('number');
        return $number;
    }


    /**
     * 会员中心
     */
    public function index()
    {
        $uid = $this->auth->id;
        $this->view->engine->layout(false);

        $row = \app\admin\model\User::find($uid);
        $service = $row['service'];
        //代母表單数据
        $surrogate_form_data = [];
        //捐卵人表單数据
        $donor_form_data = [];
        $step = 0;
        if ($service==1){
            //pre_screen
            $form_pre_screen_complate_number = PreScreen::where('uid',$uid)->value('form_complate_number');
            $form_pre_screen_number = $this->number('pre_screen',$service);
            $form_pre_screen_lv = $form_pre_screen_complate_number/$form_pre_screen_number*100;

            //presonal_info
            $form_personal_complate_number = PersonalInfo::where('uid',$uid)->value('form_complate_number');
            $form_personal_number = $this->number('personal_info',$service);
            $form_personal_lv = $form_personal_complate_number/$form_personal_number*100;

            //obstertric_history
            $form_ob_complate_number = ObstetricHistory::where('uid',$uid)->value('form_complate_number');
            $form_ob_number = $this->number('obstetric_history',$service);
            $form_ob_lv = $form_ob_complate_number/$form_ob_number*100;

            //medical_information
            $form_medical_complate_number = MedicalInformation::where('uid',$uid)->value('form_complate_number');
            $form_medical_number = $this->number('medical_information',$service);
            $form_medical_lv = $form_medical_complate_number/$form_medical_number*100;

            //about_surrogacy
            $form_about_complate_number = AboutSurrogacy::where('uid',$uid)->value('form_complate_number');
            $form_about_number = $this->number('about_surrogacy',$service);
            $form_about_lv = $form_about_complate_number/$form_about_number*100;

            //other_information
            $form_other_complate_number = OtherInformation::where('uid',$uid)->value('form_complate_number');
            $form_other_number = $this->number('other_information',$service);
            $form_other_lv = $form_other_complate_number/$form_other_number*100;

            //photos
            $form_photos_complate_number = SurrogatePhoto::where('uid',$uid)->value('form_complate_number');
            $form_photos_number = $this->number('surrogate_photo',$service);
            $form_photos_lv = $form_photos_complate_number/$form_photos_number*100;

            //health_record_release
            $form_health_complate_number = HealthRecordRelease::where('uid',$uid)->value('form_complate_number');
            $form_health_number = $this->number('surrogate_photo',$service);
            $form_health_lv = $form_health_complate_number/$form_health_number*100;

            //所有表单完成状态
            if ($form_pre_screen_lv==100&&$form_personal_lv==100&&$form_ob_lv==100&&$form_medical_lv==100
                &&$form_about_lv==100&&$form_other_lv==100&&$form_photos_lv==100
                &&$form_health_lv==100){
                $step = 1;
            }
            $surrogate_form_data = [
                'pre_screen'=>[
                  'form_complate_number'=>$form_pre_screen_complate_number?$form_pre_screen_complate_number:0,
                  'form_number'=>$form_pre_screen_number,
                  'lv'=>$form_pre_screen_lv,
                ],
                'personal'=>[
                    'form_complate_number'=>$form_personal_complate_number?$form_personal_complate_number:0,
                    'form_number'=>$form_personal_number,
                    'lv'=>$form_personal_lv,
                ],
                'ob'=>[
                    'form_complate_number'=>$form_ob_complate_number?$form_ob_complate_number:0,
                    'form_number'=>$form_ob_number,
                    'lv'=>$form_ob_lv,
                ],
                'medical'=>[
                    'form_complate_number'=>$form_medical_complate_number?$form_medical_complate_number:0,
                    'form_number'=>$form_medical_number,
                    'lv'=>$form_medical_lv,
                ],
                'about'=>[
                    'form_complate_number'=>$form_about_complate_number?$form_about_complate_number:0,
                    'form_number'=>$form_medical_number,
                    'lv'=>$form_medical_lv,
                ],
                'other'=>[
                    'form_complate_number'=>$form_other_complate_number?$form_other_complate_number:0,
                    'form_number'=>$form_other_number,
                    'lv'=>$form_other_lv,
                ],
                'photos'=>[
                    'form_complate_number'=>$form_photos_complate_number?$form_photos_complate_number:0,
                    'form_number'=>$form_photos_number,
                    'lv'=>$form_photos_lv,
                ],
                'health'=>[
                    'form_complate_number'=>$form_health_complate_number?$form_health_complate_number:0,
                    'form_number'=>$form_health_number,
                    'lv'=>$form_health_lv,
                ],
            ];
        }elseif ($service==2){
            //pre_screen
            $form_pre_screen_complate_number = DonorPreScreen::where('uid',$uid)->value('form_complate_number');
            $form_pre_screen_number = $this->number('pre_screen',$service);
            $form_pre_screen_lv = $form_pre_screen_complate_number/$form_pre_screen_number*100;

            //photos
            $form_photos_complate_number = DonorPhotos::where('uid',$uid)->value('form_complate_number');
            $form_photos_number = $this->number('photos',$service);
            $form_photos_lv = $form_photos_complate_number/$form_photos_number*100;

            //personal
            $form_personal_complate_number = DonorPersonal::where('uid',$uid)->value('form_complate_number');
            $form_personal_number = $this->number('personal',$service);
            $form_personal_lv = $form_personal_complate_number/$form_personal_number*100;

            //education
            $form_education_complate_number = DonorEducation::where('uid',$uid)->value('form_complate_number');
            $form_education_number = $this->number('education',$service);
            $form_education_lv = $form_education_complate_number/$form_education_number*100;

            //character
            $form_character_complate_number = DonorCharacter::where('uid',$uid)->value('form_complate_number');
            $form_character_number = $this->number('character',$service);
            $form_character_lv = $form_character_complate_number/$form_character_number*100;

            $form_medical_complate_number = DonorMedical::where('uid',$uid)->value('form_complate_number');
            $form_medical_number = $this->number('medical',$service);
            $form_medical_lv = $form_medical_complate_number/$form_medical_number*100;

            $donor_form_data = [
                'pre_screen'=>[
                    'form_complate_number'=>$form_pre_screen_complate_number,
                    'form_number'=>$form_pre_screen_number,
                    'lv'=>$form_pre_screen_lv,
                ],
                'photos'=>[
                    'form_complate_number'=>$form_photos_complate_number,
                    'form_number'=>$form_photos_number,
                    'lv'=>$form_photos_lv,
                ],
                'personal'=>[
                    'form_complate_number'=>$form_personal_complate_number,
                    'form_number'=>$form_personal_number,
                    'lv'=>$form_personal_lv,
                ],
                'education'=>[
                    'form_complate_number'=>$form_education_complate_number,
                    'form_number'=>$form_education_number,
                    'lv'=>$form_education_lv,
                ],
                'character'=>[
                    'form_complate_number'=>$form_character_complate_number,
                    'form_number'=>$form_character_number,
                    'lv'=>$form_character_lv,
                ],
                'medical'=>[
                    'form_complate_number'=>$form_medical_complate_number,
                    'form_number'=>$form_medical_number,
                    'lv'=>$form_medical_lv,
                ],
            ];

            if ($form_pre_screen_lv==100&&$form_photos_lv==100&&$form_personal_lv==100&&$form_education_lv==100
                &&$form_character_lv==100&&$form_medical_lv==100){
                $step = 1;
            }
        }
        $this->view->assign('row', $row);
        $this->view->assign('step', $step);
        $this->view->assign('surrogate_form_data', $surrogate_form_data);
        $this->view->assign('donor_form_data', $donor_form_data);
        return $this->view->fetch();
    }

    /**
     * 注册会员
     */
    public function register($aff='')
    {
        $this->view->assign("aff", $aff);
        if ($this->auth->id) {
            $this->success(__('You\'ve logged in, do not login again'), url('user/index'));
        }
        if ($this->request->isPost()) {

            $first_name = $this->request->post('first_name');
            $last_name = $this->request->post('last_name');
            $email = $this->request->post('email');
            $mobile = $this->request->post('mobile');
            $data = [
                'first_name'=>$first_name,
                'last_name'=>$last_name,
                'email'=>$email,
                'mobile'=>$mobile,
                'service'=>1,
                'lead_source' => 'vip.dopusa.com',
            ];
            $model = \app\admin\model\PreScreenForm::where('email',$email)->find();
            if ($model){
                $data['is_repeat'] = 2;
            }
            //添加分配者
            if ($model){
                if ($model['admin_id']){
                    $data['admin_id'] = $model['admin_id'];
                }else{
                    $admin_id = owner();
                    $data['admin_id'] = $admin_id;
                }
            }else{
                $admin_id = owner();
                $data['admin_id'] = $admin_id;
            }
            $res = \app\admin\model\PreScreenForm::create($data);
            if ($res){
                $this->redirect('index/index/thank');
            }else{
                $this->error('Registration failed, please contact the administrator');
            }

        }
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }


    function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
    /**
     * 会员登录
     */
    public function login($aff='',$service='1')
    {
        $this->view->assign("aff", $aff);
        $this->view->assign("service", $service);
        if ($this->auth->id) {
            $this->success(__('You\'ve logged in, do not login again'),  url('user/index'));
        }
        if ($this->request->isPost()) {
            $account = $this->request->post('account');
            $password = $this->request->post('password');
            $service = $this->request->post('service');
            $keeplogin = (int)$this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'account'   => 'require',
                'password'  => 'require',
                '__token__' => 'require|token',
            ];

            $msg = [
                'account.require'  => 'Email can not be empty',
                'password.require' => 'Password can not be empty',
            ];
            $data = [
                'account'   => $account,
                'password'  => $password,
                '__token__' => $token,
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
                return false;
            }
            if ($this->auth->login($account, $password,$service)) {
                    $this->success(__('Logged in successful'), url('user/index'));

            } else {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        if ($this->request->isPost()) {
            $this->token();
            //退出本站
            $this->auth->logout();
            $this->success(__('Logout successful'), url('user/login'));
        }
        $html = "<form id='logout_submit' name='logout_submit' action='' method='post'>" . token() . "<input type='submit' value='ok' style='display:none;'></form>";
        $html .= "<script>document.forms['logout_submit'].submit();</script>";
        $this->view->engine->layout(false);
        return $html;
    }

    /**
     * 个人信息
     */
    public function profile()
    {
        $uid = $this->auth->id;
        $row = \app\admin\model\User::find($uid);
        $pre_screening = PreScreen::where('uid',$uid)->find();
        $images = SurrogatePhoto::where('uid',$uid)->value('images');
        if (isset($images)){
            $images = explode(',',$images);
        }

        $this->view->assign('title', __('Profile'));
        $this->view->assign('pre_screening',$pre_screening);
        $this->view->assign('images',$images);
        $this->view->assign('row',$row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function edit_profile(){
        $uid = $this->auth->id;
        $row = \app\admin\model\User::find($uid);

        if ($this->request->isPost()) {
            $params = $this->request->post();
            $validate = $this->validate($params, [
                'first_name'=>'require',
                'last_name'=>'require',
                'email'=>'require|email|unique:user,email,'.$uid,
                'mobile'=>'require',
            ],[
                'first_name.require' => 'First name cannot be empty',
                'last_name.require' => 'Last name cannot be empty',
                'email.require' => 'Email cannot be empty',
                'email.email' => 'Incorrect mailbox format',
                'mobile.require' => 'Phone cannot be empty',
            ]);
            if (true !== $validate) {
                $this->error($validate);
            }
            \app\admin\model\User::where('id',$uid)->update($params);

            $this->success('successful','index/user/profile');
        }


        $this->view->engine->layout(false);
        $this->view->assign('row',$row);
        return $this->view->fetch();
    }


    /**
     * 修改密码
     */
    public function changepwd()
    {
        if ($this->request->isPost()) {
            $oldpassword = $this->request->post("oldpassword");
            $newpassword = $this->request->post("newpassword");
            $renewpassword = $this->request->post("renewpassword");
            $token = $this->request->post('__token__');
            $rule = [
                'oldpassword'   => 'require|regex:\S{6,30}',
                'newpassword'   => 'require|regex:\S{6,30}',
                'renewpassword' => 'require|regex:\S{6,30}|confirm:newpassword',
                '__token__'     => 'token',
            ];

            $msg = [
                'renewpassword.confirm' => __('Password and confirm password don\'t match')
            ];
            $data = [
                'oldpassword'   => $oldpassword,
                'newpassword'   => $newpassword,
                'renewpassword' => $renewpassword,
                '__token__'     => $token,
            ];
            $field = [
                'oldpassword'   => __('Old password'),
                'newpassword'   => __('New password'),
                'renewpassword' => __('Renew password')
            ];
            $validate = new Validate($rule, $msg, $field);
            $result = $validate->check($data);
            if (!$result) {
                $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
                return false;
            }

            $ret = $this->auth->changepwd($newpassword, $oldpassword);
            if ($ret) {
                $this->success(__('Reset password successful'), url('user/login'));
            } else {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }
        $this->view->assign('title', __('Change password'));
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function attachment()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $mimetypeQuery = [];
            $where = [];
            $filter = $this->request->request('filter');
            $filterArr = (array)json_decode($filter, true);
            if (isset($filterArr['mimetype']) && preg_match("/(\/|\,|\*)/", $filterArr['mimetype'])) {
                $this->request->get(['filter' => json_encode(array_diff_key($filterArr, ['mimetype' => '']))]);
                $mimetypeQuery = function ($query) use ($filterArr) {
                    $mimetypeArr = array_filter(explode(',', $filterArr['mimetype']));
                    foreach ($mimetypeArr as $index => $item) {
                        $query->whereOr('mimetype', 'like', '%' . str_replace("/*", "/", $item) . '%');
                    }
                };
            } elseif (isset($filterArr['mimetype'])) {
                $where['mimetype'] = ['like', '%' . $filterArr['mimetype'] . '%'];
            }

            if (isset($filterArr['filename'])) {
                $where['filename'] = ['like', '%' . $filterArr['filename'] . '%'];
            }

            if (isset($filterArr['createtime'])) {
                $timeArr = explode(' - ', $filterArr['createtime']);
                $where['createtime'] = ['between', [strtotime($timeArr[0]), strtotime($timeArr[1])]];
            }
            $search = $this->request->get('search');
            if ($search) {
                $where['filename'] = ['like', '%' . $search . '%'];
            }

            $model = new Attachment();
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            $total = $model
                ->where($where)
                ->where($mimetypeQuery)
                ->where('user_id', $this->auth->id)
                ->order("id", "DESC")
                ->count();

            $list = $model
                ->where($where)
                ->where($mimetypeQuery)
                ->where('user_id', $this->auth->id)
                ->order("id", "DESC")
                ->limit($offset, $limit)
                ->select();
            $cdnurl = preg_replace("/\/(\w+)\.php$/i", '', $this->request->root());
            foreach ($list as $k => &$v) {
                $v['fullurl'] = ($v['storage'] == 'local' ? $cdnurl : $this->view->config['upload']['cdnurl']) . $v['url'];
            }
            unset($v);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $mimetype = $this->request->get('mimetype', '');
        $mimetype = substr($mimetype, -1) === '/' ? $mimetype . '*' : $mimetype;
        $this->view->assign('mimetype', $mimetype);
        $this->view->assign("mimetypeList", \app\common\model\Attachment::getMimetypeList());
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function pre_screen(){
        $this->view->engine->layout(false);
        $uid = $this->auth->id;

        $row = PreScreen::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','pre_screen')->value('number');
        $bir_time = 0;
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;

            if ($row['birthday_time']){
                $bir_time = $row['birthday_time'];
                $bir_time = $this->time($bir_time);
            }
           
        }else{
            $lv = 0;
        }

        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($uid);die;

            $validate = $this->validate($params, [
                'email' => 'require|unique:user,email,'.$uid,
            ],[
                'email.require' => 'Email cannot be empty',
                'email.unique' => 'Email already exists',
            ]);
            if (true !== $validate) {
                $this->error($validate);
            }


//            dump($params);die;
            $model = PreScreen::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }
            if ($params['height_ft']&&$params['height_in']){
                $ft = $params['height_ft'];
                $in = $params['height_in'];
                $lbs = $params['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $params['bmi'] = round($bmi);
//                dump($bmi);die;
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];
            $params['birthday_time'] = $this->strtotime($params['birthday_time']);
            if ($model){

                PreScreen::where('uid',$uid)->update($params);

                $user= [
                    'first_name'=>$params['first_name'],
                    'last_name'=>$params['last_name'],
                    'email'=>$params['email'],
                    'mobile'=>$params['mobile'],
                ];
                \app\admin\model\User::where('id',$uid)->update($user);

            }else{
                PreScreen::create($params);
            }
            $this->success('successful');
        }
        $examine_pre_screen = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_pre_screen = $examine['pre_screen'];
        }
        $this->view->assign("examine_pre_screen",$examine_pre_screen);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("bir_time",$bir_time?$bir_time:0);

        return $this->view->fetch();
    }

    public function personal_info(){
        $this->view->engine->layout(false);

        $uid = $this->auth->id;
        $row = PersonalInfo::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','personal_info')->value('number');

        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            
            $model = PersonalInfo::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                PersonalInfo::where('uid',$uid)->update($params);
            }else{
                PersonalInfo::create($params);
            }
            $this->success('successful');
        }

        $examine_personal_info = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_personal_info = $examine['personal_info'];
        }
        $this->view->assign("examine_personal_info", $examine_personal_info);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }



    public function  surrogate_photo(){
        $this->view->engine->layout(false);

        $uid = $this->auth->id;
        $row = SurrogatePhoto::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','surrogate_photo')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $model = SurrogatePhoto::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }
            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                SurrogatePhoto::where('uid',$uid)->update($params);
            }else{
                SurrogatePhoto::create($params);
            }
            $this->success('ok');
        }
        $examine_photos = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_photos = $examine['photos'];
        }
        $this->view->assign("examine_photos",$examine_photos);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function obstetric_history(){
        $this->view->engine->layout(false);

        $uid = $this->auth->id;

        $row = ObstetricHistory::where('uid',$uid)->find();
        if ($row){
            $row['last_period_time'] = $this->time($row['last_period_time']);
        }


//        $surrogate_baby_model = SurrogateBaby::where('uid',$uid)->order('baby','asc')->column('id,uid,birthday_time,sex,weight,height,fetal_age,
//        pregnancy_type,complications,baby');

        $surrogate_baby_model = SurrogateBaby::where('uid',$uid)->order('baby','asc')->select();

        $surrogate_baby_data = [];
        $button_number = '';
        if ($surrogate_baby_model){
            foreach ($surrogate_baby_model as $k=>$v){
                $surrogate_baby_data[] = $v;
            }
            foreach ($surrogate_baby_data as $k=>$v){
//                $surrogate_baby_data[$k]['birthday_time'] = $this->time($v['birthday_time']);
                $surrogate_baby_data[$k]['birthday_time_a'] = $this->time($v['birthday_time']);
            }
            $button_number = count($surrogate_baby_data);

        }
//          dump($surrogate_baby_data);die;
        // 表单总数
        $form_number = FormNumber::where('name','obstetric_history')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();

            //            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }
            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            $strtotime = [];
            if (isset($params['birthday_time']) ){

                foreach ($params['birthday_time'] as $v){
                    if ($v){
                        $strtotime[] = $this->strtotime($v);
                    }else{
                        $strtotime[]='';
                    }
                }

                $count = count($params['birthday_time']);
                $baby_ids =[];
                for ($i=0;$i<$count;$i++){
                    $boby_data = [];
                    $boby_data['birthday_time'] = $strtotime[$i];
                    $boby_data['sex'] = $params['sex'][$i];
                    $boby_data['weight'] = $params['weight'][$i];
                    $boby_data['height'] = $params['height'][$i];
                    $boby_data['fetal_age'] = $params['fetal_age'][$i];
                    $boby_data['pregnancy_type'] = $params['pregnancy_type'][$i];
                    $boby_data['complications'] = $params['complications'][$i];
                    $boby_data['uid'] = $params['uid'];
                    $boby_data['baby'] = $params['baby'][$i];
                    $boby_data['is_surrogacy_pregnancy'] = $params['is_surrogacy_pregnancy'][$i];

                    $where= [];
                    $where=[
                        'uid'=>$uid,
                        'baby'=>$params['baby'][$i],
                    ];
                    $baby_model = SurrogateBaby::where($where)->value('id');

                    if ($baby_model){
                        SurrogateBaby::where($where)->update($boby_data);
                    }else{
                        SurrogateBaby::create($boby_data);
                    }
                    array_push($baby_ids,$params['baby'][$i]);
                }
                $model_count = SurrogateBaby::where('uid',$uid)->count();
                if ($model_count>$count){
                    $bids = SurrogateBaby::where('uid',$uid)->where('baby','not in',$baby_ids)->column('id');
                    if ($bids){
                        foreach ($bids as $v){
                            SurrogateBaby::where('id',$v)->delete();
                        }
                    }
                }

            }else{
                $boby_ids = SurrogateBaby::where('uid',$uid)->column('id');
                if ($boby_ids){
                    foreach ($boby_ids as $v){
                        SurrogateBaby::where('id',$v)->delete();
                    }
                }

            }
//             dump($params);die;
            $data['form_complate_number'] =  $params['form_complate_number'] ;
            $data['uid'] = $params['uid'];
            $data['last_period_time'] = $this->strtotime($params['last_period_time']);
            $data['days_of_bleed'] = $params['days_of_bleed'];
            $data['days_between'] = $params['days_between'];
            $data['is_menstruation_regular'] = $params['is_menstruation_regular'];
            $data['amount_of_bleed'] = $params['amount_of_bleed'];
            $data['smear_result'] = $params['smear_result'];

            $data['is_artificial_abortion'] = $params['is_artificial_abortion'];
            $data['artificial_abortion_content'] = $params['artificial_abortion_content'];
            $data['is_spontaneous_abortion'] = $params['is_spontaneous_abortion'];
            $data['spontaneous_abortion_content'] = $params['spontaneous_abortion_content'];
            $data['status'] = $params['status'];
            $baby_model = ObstetricHistory::where('uid',$uid)->find();

            if ($baby_model){
                ObstetricHistory::where('uid',$uid)->update($data);
            }else{
                ObstetricHistory::create($data);
            }
            $this->success('successful');



        }
        $examine_ob = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_ob = $examine['ob'];
        }
        $this->view->assign("examine_ob",$examine_ob);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("surrogate_baby_data", $surrogate_baby_data);
        $this->view->assign("button_number", $button_number);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);

        return $this->view->fetch();
    }

    public function medical_information(){

        $this->view->engine->layout(false);
        $uid = $this->auth->id;
        $row = MedicalInformation::where('uid',$uid)->find();
        $row = $row?$row->toArray():$row;
        // 表单总数
        $form_number = FormNumber::where('name','medical_information')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
            if ($row['any_diseases']){
                $row['any_diseases'] = explode(',',$row['any_diseases']);
            }else{
                $row['any_diseases'] = array();
            }


        }else{
            $lv = 0;
        }
//        dump($row);die;
        if ($this->request->isPost()) {
            $params = $this->request->post();


            if (isset($params['any_diseases'])){
                $params['any_diseases'] = implode(',',$params['any_diseases']);
            }else{
                $params['any_diseases'] = '';
            }

            $model = MedicalInformation::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                MedicalInformation::where('uid',$uid)->update($params);
            }else{
                MedicalInformation::create($params);
            }
            $this->success('successful');
        }


        $examine_medical = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_medical = $examine['medical'];
        }
        $this->view->assign("examine_medical",$examine_medical);

        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function about_surrogacy(){

        $this->view->engine->layout(false);
        $uid = $this->auth->id;
        $row = AboutSurrogacy::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','about_surrogacy')->value('number');
        $bir_time = '';
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
            if ($row['parents_type']){
                $row['parents_type'] = explode(',',$row['parents_type']);
            }
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $model = AboutSurrogacy::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }

            if (isset($params['parents_type'])){
                $params['parents_type'] = implode(',',$params['parents_type']);
            }else{
                $params['parents_type'] = '';
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                AboutSurrogacy::where('uid',$uid)->update($params);
            }else{
                AboutSurrogacy::create($params);
            }
            $this->success('successful');
        }

        $examine_about = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_about = $examine['about'];
        }

        $this->view->assign("examine_about",$examine_about);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function other_information(){
        $this->view->engine->layout(false);

        $uid = $this->auth->id;
        $row = OtherInformation::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','other_information')->value('number');

        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $model = OtherInformation::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                OtherInformation::where('uid',$uid)->update($params);
            }else{
                OtherInformation::create($params);
            }
            $this->success('successful');
        }
        $examine_other = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_other = $examine['other'];
        }
        $this->view->assign("examine_other",$examine_other);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function health_record_release(){
        $this->view->engine->layout(false);
        $uid = $this->auth->id;
        $row = HealthRecordRelease::where('uid',$uid)->find();
        $regnancy_informatiom_model = RegnancyInformation::where('uid',$uid)->order('number','asc')->select();
        $regnancy_informatiom_data = [];
        $button_number = '';
        if ($regnancy_informatiom_model){
            foreach ($regnancy_informatiom_model as $k=>$v){
                $regnancy_informatiom_data[] = $v;
            }
            foreach ($regnancy_informatiom_data as $k=>$v){
                if ($v['delivery_date']){
                    $regnancy_informatiom_data[$k]['delivery_date'] = $this->time($v['delivery_date']);
                }

            }
            $button_number = count($regnancy_informatiom_data);
        }

        // 表单总数
        $form_number = FormNumber::where('name','health_record_release')->value('number');

        if ($row){
//            dump(1);
            $lv = $row['form_complate_number']/$form_number*100;
//            $row['canvas_time_a'] = $this->time($row['canvas_time']);
//            $row['name_time_a'] = $this->time($row['name_time']);
        }else{
            $lv = 0;
        }

        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $strtotime1 = [];
            if (isset($params['protect_time']) ){

                foreach ($params['delivery_date'] as $v1){
                    if ($v1){
                        $strtotime1[] = $this->strtotime($v1);
                    }else{
                        $strtotime1[]='';
                    }
                }

                $count = count($params['protect_time']);
                $baby_ids = [];
                for ($i=0;$i<$count;$i++){
                    $regnancy_data = [];
                    $regnancy_data['protect_time'] = $params['protect_time'][$i];
                    $regnancy_data['maternity_institution'] = $params['maternity_institution'][$i];
                    $regnancy_data['doctor'] = $params['doctor'][$i];
                    $regnancy_data['address_one'] = $params['address_one'][$i];
                    $regnancy_data['city'] = $params['city'][$i];
//                    $regnancy_data['country'] = $params['country'][$i];
                    $regnancy_data['state'] = $params['state'][$i];
                    $regnancy_data['code'] = $params['code'][$i];
                    $regnancy_data['hospital_fax'] = $params['hospital_fax'][$i];
                    $regnancy_data['hospital_phone'] = $params['hospital_phone'][$i];
                    $regnancy_data['delivery_date'] = $strtotime1[$i];
                    $regnancy_data['delivery_hospital_name'] = $params['delivery_hospital_name'][$i];
                    $regnancy_data['address_line'] = $params['address_line'][$i];
                    $regnancy_data['city_two'] = $params['city_two'][$i];
                    $regnancy_data['state_two'] = $params['state_two'][$i];
//                    $regnancy_data['country_two'] = $params['country_two'][$i];
                    $regnancy_data['code_two'] = $params['code_two'][$i];
                    $regnancy_data['hospital_phone_two'] = $params['hospital_phone_two'][$i];
                    $regnancy_data['hospital_fax_two'] = $params['hospital_fax_two'][$i];
                    $regnancy_data['uid'] = $uid;
                    $regnancy_data['number'] = $params['number'][$i];

                    $where=[
                        'uid'=>$uid,
                        'number'=>$params['number'][$i],
                    ];
                    $baby_model = RegnancyInformation::where($where)->value('id');
                    if ($baby_model){
                        RegnancyInformation::where($where)->update($regnancy_data);
                    }else{
                        RegnancyInformation::create($regnancy_data);
                    }

                    array_push($baby_ids,$params['number'][$i]);
                }
                $model_count = RegnancyInformation::where('uid',$uid)->count();
                if ($model_count>$count){
                    $bids = RegnancyInformation::where('uid',$uid)->where('number','not in',$baby_ids)->column('id');
                    if ($bids){
                        foreach ($bids as $v){
                            RegnancyInformation::where('id',$v)->delete();
                        }
                    }
                }
            }else{
                $boby_ids = RegnancyInformation::where('uid',$uid)->column('id');
                if ($boby_ids){
                    foreach ($boby_ids as $v){
                        RegnancyInformation::where('id',$v)->delete();
                    }
                }
            }




//            if (isset($params['canvas'])&&$params['canvas_time']!=''){
//                $params['canvas'] = $this->saveBase64($params['canvas']);
//            }
//            if (isset($params['canvas_time'])&&$params['canvas_time']!=''){
//                $params['canvas_time'] = $this->strtotime($params['canvas_time']);
//            }
//            if (isset($params['name_time'])&&$params['canvas_time']!=''){
//            if (isset($params['name_time'])){
//                $params['name_time'] = $this->strtotime($params['name_time']);
//            }

            $model = HealthRecordRelease::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = 1;
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            $data = [
                'uid'=>$uid,
                'hospital'=>$params['hospital'],
                'is_one'=>$params['is_one'],
                'is_two'=>$params['is_two'],
                'is_three'=>$params['is_three'],
                'is_four'=>$params['is_four'],
                'is_five'=>$params['is_five'],
//                'canvas'=>$params['canvas'],
//                'canvas_time'=>$params['canvas_time'],
//                'name'=>$params['name'],
//                'name_time'=>$params['name_time'],
                'form_complate_number'=>$params['form_complate_number'],
                'status'=>$params['status'],
            ];
//            dump($data);die;
            if ($model){
                HealthRecordRelease::where('uid',$uid)->update($data);
            }else{
                HealthRecordRelease::create($data);
            }
            $this->success('successful');
        }

        $examine_health = 0;
        $examine = Examine::where('uid',$uid)->find();
        if ($examine){
            $examine_health = $examine['health'];
        }
        $this->view->assign("examine_health",$examine_health);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("regnancy_informatiom_data", $regnancy_informatiom_data);
        $this->view->assign("button_number", $button_number);

        return $this->view->fetch();
    }


    function saveBase64($base64_img)
    {
        //目录的upload文件夹下
        $up_dir = ROOT_PATH . "public/uploads/".date('Ymd', time()) . "/";  //创建目录
        if(!file_exists($up_dir)){
            mkdir($up_dir,0777,true);
        }
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
            $type = $result[2];
            if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
                $name = \fast\Random::alnum(32);
                $new_file = $up_dir . $name . '.' . $type;
                if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
                    return "/uploads/" . date('Ymd', time()) . "/" . $name . '.' . $type;
                }
            }
        }
        return false;
    }

    public function time($time){
        $date  = date('Y-m-d', $time);
        $date = explode('-',$date);
        $a = $date[1].'/'.$date[2].'/'.$date[0];
        return $a;
    }
    public function strtotime($time){
        $b_time = explode('/',$time);
        $b_time = $b_time[2].'-'.$b_time[0].'-'.$b_time[1];
        $strtotime = strtotime($b_time);
        return $strtotime;
    }

    public function ws(){
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function saveSignature(){
        $attachment = null;
        //默认普通上传文件
        $image = urldecode($this->request->param('file'));

        $temp = './uploads/signature/'.date('Ymd');

        if(!file_exists($temp)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($temp, 0700);
        }
        $temp = $temp . '/'. \fast\Random::alnum(32).'.png';
        if (strstr($image,",")){
            $image = explode(',',$image);
            $image = $image[1];
        }

        $res = file_put_contents($temp, base64_decode(($image)));//返回的是字节数

        if($res)
        {
            $this->success(__('Uploaded successful'), '', ['url' => substr($temp,1), 'fullurl' => cdnurl(substr($temp,1), true)]);
        }else{
            $this->error('稍后重试');
        }

    }

    public function forgot_password($aff=''){
        $this->view->assign("aff", $aff);
        return $this->view->fetch();
    }

    public function pass_email(){
        $this->view->engine->layout(false);
        $params = input();
        $email = $params['email'];
        $user = \app\admin\model\User::where('email',$email)->find();
        if (!$user){
            return  json(['code'=>3,'msg'=>'User does not exist',]);
        }
        //发送邮件
        $admin = 'admin';
        $uid = 1;
        $mail =$email ;
//                    $title = '恭喜您注册代母网站成功';
        $title = 'Change your website login password';
        $type =4;
        $pass ='123456';

        $username = $user['first_name'].$user['last_name'];
        $ret = send_email($admin,$uid,$mail,$title,$type,$pass,$username);

        if ($ret['code'] !=1){
            return  json(['code'=>2,'msg'=>'Sending email failed','data'=>$ret]);
        }else{
            return  json(['code'=>1,'msg'=>'Successfully sent email','data'=>$ret]);
        }
    }

    public function forgot_password_edit($email=''){
        $this->view->engine->layout(false);
        if ($this->request->isPost()) {

            $email = input('email');
            $password = input('password');
            $salt = Random::alnum();
            $newpassword= $this->getEncryptPassword($password, $salt);
            $model = \app\admin\model\User::where('email',$email)->find();
            if (!$model){
                $this->error('Account does not exist');
            }
            $ret = \app\admin\model\User::where('email',$email)->update(['loginfailure' => 0, 'password' => $newpassword, 'salt' => $salt]);
            if ($ret) {
                $this->success(__('Reset password successful'), url('user/login'));
            } else {
                $this->error($this->auth->getError());
            }

        }
        $this->view->assign("email", $email);
        return $this->view->fetch();
    }

    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }
}
