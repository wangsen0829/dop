<?php

namespace app\admin\controller\user;
use app\admin\model\DonorCharacter;
use app\admin\model\DonorEducation;
use app\admin\model\DonorEthnicity;
use app\admin\model\DonorExamine;
use app\admin\model\DonorMedical;
use app\admin\model\DonorPersonal;
use app\admin\model\DonorPhotos;
use app\admin\model\DonorPreScreen;
use app\admin\model\DonorQinshu;
use app\admin\model\DonorQinshuDisease;
use app\admin\model\NotesDonor;
use app\admin\model\RegnancyInformation;
use app\admin\model\AboutSurrogacy;
use app\admin\model\Examine;
use app\admin\model\SurrogateBaby;
use app\admin\model\Background;
use app\admin\model\AdditionalInformation;
use app\admin\model\FormNumber;
use app\admin\model\HealthRecordRelease;
use app\admin\model\MedicalInformation;
use app\admin\model\ObstetricHistory;
use app\admin\model\OtherInformation;
use app\admin\model\PersonalInfo;
use app\admin\model\PreScreen;
use app\admin\model\SurrogatePhoto;
use app\admin\model\Notes;
use app\admin\model\NotesSurrogacy;
use app\common\controller\Backend;
use app\common\library\Auth;
use app\admin\model\AuthGroupAccess;
use app\admin\model\SurrogacySbp;
use fast\Random;

use think\Config;
use think\Db;
use think\db\exception\BindParamException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

use think\Hook;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Donor extends Backend
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $relationSearch = true;
    protected $searchFields = 'id,email,mobile,first_name,last_name';

    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');

        $this->assignconfig("url",Config::get('site.url') );

        //筛选角色是员工
        $uid = AuthGroupAccess::where('group_id',7)->column('uid');
        $admin = \app\admin\model\Admin::order('id','asc')->where('id','in',$uid)->select();
        $this->view->assign("admin", $admin);

        $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
        $this->view->assign("role_id", $role_id);
    }

    /**
     * 查看
     */
    public function index()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);

        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是Selectpage，则转发到Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }

        $filter = json_decode($this->request->get('filter'), true);
        $op = json_decode($this->request->get("op"),true);
        $where1 = [];
        if (isset($filter['bio'])){
            $arr = explode(',',$filter['bio']);
            foreach ($arr as $v){
                $key =  $v.'.status';
                $where_p[$key] = '1';
                unset($filter[$v]);
            }
            $form_ids = $this->model
                ->with('group,admin,donor_prescreen,donor_personal,donor_photos,education,character,donor_medical,donor_examine')
                ->where($where_p)
                ->order('user.createtime','desc')->column('user.id');
            $form_ids = implode(',',$form_ids);

            unset($filter['bio']);
            unset($op['bio']);
            $filter['id'] = $form_ids;
            $op['id'] = "in";
            $this->request->get(["filter"=>json_encode($filter),'op'=>json_encode($op)]);
        }
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        $where1['user.id'] = array('not in','1');
        $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
        if ($role_id==7){
            $where1['user.admin_id'] = $this->auth->id;
        }
        $list = $this->model
            ->with('group,admin,donor_prescreen,donor_personal,donor_photos,education,character,donor_medical,donor_examine')
            ->where('service','2')
            ->where($where)
            ->where($where1)
            ->order($sort, $order)
            ->paginate($limit);
        foreach ($list as $k => $v) {
            $v->avatar = $v->avatar ? cdnurl($v->avatar, true) : letter_avatar($v->nickname);
            $v->hidden(['password', 'salt']);
            $progress = $this->form_pregress($v->id);
            $v->forms = $progress;
            \app\admin\model\User::where('id',$v->id)->update(['forms'=>$progress]);
        }
        $result = array("total" => $list->total(), "rows" => $list->items());
        return json($result);

    }

    public function form_pregress($uid=null){

        //pre_screen
        $pre = DonorPreScreen::where('uid',$uid)->value('status');
        $pre = $pre?(int)$pre:0;

        //photos
        $photos = DonorPhotos::where('uid',$uid)->value('status');
        $photos = $photos?(int)$photos:0;

        //personal
        $personal = DonorPersonal::where('uid',$uid)->value('status');
        $personal = $personal?(int)$personal:0;

        //education
        $education = DonorEducation::where('uid',$uid)->value('status');
        $education = $education?(int)$education:0;

        //character
        $character = DonorCharacter::where('uid',$uid)->value('status');
        $character = $character?(int)$character:0;


        $step = 0;
        $step = ($pre+$photos+$personal+$education+$character)/5*100;
        return intval($step);

    }

    //分配
    public function fenpei(){
        $params = input();
        $admin_id = $params['category'];
        $form_ids = $params['ids'];
        $form_ids = explode(',',$form_ids);
        foreach ($form_ids as $v){
            $res = $this->model->where('id',$v)->update(['admin_id'=>$admin_id]);
        }
        if ($res){
            return json(['code'=>1,'msg'=>'Assigned successfully','res'=>$res]);
        }else{
            return json(['code'=>0,'msg'=>'allocation failure']);
        }
    }

    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);

        $service = $row['service'];
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $this->success("Ajax请求成功", null, ['id' => $ids]);
        }

        $this->view->engine->layout(false);

        $form_pre_screen_complate_number = DonorPreScreen::where('uid',$ids)->value('form_complate_number');
        $form_pre_screen_number = $this->number('pre_screen',$service);
        $form_pre_screen_lv = $form_pre_screen_complate_number/$form_pre_screen_number*100;

        //personal
        $form_personal_complate_number = DonorPersonal::where('uid',$ids)->value('form_complate_number');
        $form_personal_number = $this->number('personal',$service);
        $form_personal_lv = $form_personal_complate_number/$form_personal_number*100;

        //photos
        $form_photos_complate_number = DonorPhotos::where('uid',$ids)->value('form_complate_number');
        $form_photos_number = $this->number('photos',$service);
        $form_photos_lv = $form_photos_complate_number/$form_photos_number*100;

        //education
        $form_education_complate_number = DonorEducation::where('uid',$ids)->value('form_complate_number');
        $form_education_number = $this->number('education',$service);
        $form_education_lv = $form_education_complate_number/$form_education_number*100;

        //character
        $form_character_complate_number = DonorCharacter::where('uid',$ids)->value('form_complate_number');
        $form_character_number = $this->number('character',$service);
        $form_character_lv = $form_character_complate_number/$form_character_number*100;

        $form_medical_complate_number = DonorMedical::where('uid',$ids)->value('form_complate_number');
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
        $pre_screen = DonorPreScreen::where('uid',$ids)->find();
        $this->view->assign('pre_screen', $pre_screen);

        $donor_photo = DonorPhotos::where('uid',$ids)->find();
        if ($donor_photo){
            $donor_photo['full_images'] = explode(',',$donor_photo['full_images']);
            $donor_photo['child_images'] = explode(',',$donor_photo['child_images']);
            $donor_photo['video_images'] = explode(',',$donor_photo['video_images']);
        }


        $this->view->assign('donor_form_data', $donor_form_data);
        $this->view->assign('donor_photo', $donor_photo);
        $this->view->assign('row', $row);
        $this->view->assign('ids', $ids);
        return $this->view->fetch();
    }

    public function number($name='',$type=''){
        $number = FormNumber::where('name',$name)->where('type',$type)->value('number');
        return $number;
    }

    public function pre_screen(){

        $uid = input('uid');
        $row = DonorPreScreen::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('type','2')->where('name','pre_screen')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//
            $model = DonorPreScreen::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']=="0"){
                $params['status'] = '1';
            }
            $params['form_complate_number'] = $form_number - intval($params['form_complate_number']);

            if ($model){
                DonorPreScreen::where('uid',$uid)->update($params);
                $user= [
                    'first_name'=>$params['first_name'],
                    'last_name'=>$params['last_name'],
                    'email'=>$params['email'],
                    'mobile'=>$params['mobile'],
                ];
                \app\admin\model\User::where('id',$uid)->update($user);
            }else{
                DonorPreScreen::create($params);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function photos(){
        $uid = input('uid');
        $row = DonorPhotos::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('type','2')->where('name','photos')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $model = DonorPhotos::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']=='0'){
                $params['status'] = '1';
            }
            $params['form_complate_number'] = $form_number - intval($params['form_complate_number']);
            if ($model){
                DonorPhotos::where('uid',$uid)->update($params);
            }else{
                DonorPhotos::create($params);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function personal(){
        $uid = input('uid');
        $row = DonorPersonal::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('type','2')->where('name','personal')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $model = DonorPersonal::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']==0){
                $params['status'] = '1';
            }
            $params['form_complate_number'] = $form_number - intval($params['form_complate_number']);
            if ($model){
                DonorPersonal::where('uid',$uid)->update($params);
            }else{
                DonorPersonal::create($params);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function education(){
        $uid = input('uid');
        $row = DonorEducation::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('type','2')->where('name','education')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $model = DonorEducation::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']=='0'){
                $params['status'] = '1';
            }
            $params['form_complate_number'] = $form_number - intval($params['form_complate_number']);
            if ($model){
                DonorEducation::where('uid',$uid)->update($params);
            }else{
                DonorEducation::create($params);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function character(){
        $uid = input('uid');
        $row = DonorCharacter::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('type','2')->where('name','character')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $model = DonorCharacter::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']=='0'){
                $params['status'] = '1';
            }
            $params['form_complate_number'] = $form_number - intval($params['form_complate_number']);
            if ($model){
                DonorCharacter::where('uid',$uid)->update($params);
            }else{
                DonorCharacter::create($params);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function medical(){
        $uid = input('uid');
        $this->view->engine->layout(false);
        $row = DonorMedical::where('uid',$uid)->find();
        if ($row){
            if ($row['any_diseases']){
                $row['any_diseases'] = explode(',',$row['any_diseases']);
            }else{
                $row['any_diseases'] = array();
            }
        }
        // 表单总数
        $form_number = FormNumber::where('type','2')->where('name','medical')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }


        $row_qinshu = DonorQinshu::where('uid',$uid)->select();

        $number = DonorQinshu::where('uid',$uid)->where('type',7)->max('number');
//        dump($number);die;

        $row_qinshu_disease = DonorQinshuDisease::where('uid',$uid)->find();
        $row_qinshu_disease = $row_qinshu_disease?$row_qinshu_disease->toArray():'';
        if ($row_qinshu_disease){
            foreach ($row_qinshu_disease as $k=>$v){
                if ($k == 'id' || $k == 'uid'|| $k == 'createtime'|| $k == 'updatetime'|| $k == 'deletetime'){
                    $row_qinshu_disease[$k] = $v;
                }else{
                    $a = explode(',',$v);
                    $row_qinshu_disease[$k] = $a;
                }
            }
        }

        if ($this->request->isPost()) {
            $params = $this->request->post();
            $model = DonorMedical::where('uid',$uid)->find();
            $medical = $params['medical'];
            //表单全部填写完成
            if ($params['form_complate_number']=='0'){
                $medical['status'] = '1';
            }

            $medical['form_complate_number'] = $form_number - intval($params['form_complate_number']);
            $medical['uid'] = $uid;

            if (isset($params['any_diseases'])){
                $medical['any_diseases'] = implode(',',$params['any_diseases']);
            }else{
                $medical['any_diseases'] = '';
            }

            $qinshu = $params['qinshu'];

            $value_count = count($qinshu['type']);
            $arr = [];
            for ($i=0;$i<$value_count;$i++){
                $arr[$i] = [
                    'uid' => $uid,
                    'type' => $qinshu['type'][$i],
                    'age' => $qinshu['age'][$i],
                    'height' => $qinshu['height'][$i],
                    'weight' => $qinshu['weight'][$i],
                    'hair' => $qinshu['hair'][$i],
                    'eyes' => $qinshu['eyes'][$i],
                    'race' => $qinshu['race'][$i],
                    'health' => $qinshu['health'][$i],
                    'die' => $qinshu['die'][$i],
                    'highest_education' => $qinshu['highest_education'][$i],
                    'career' => $qinshu['career'][$i],
                    'number' => $qinshu['number'][$i],
                ];
            }

//             dump($arr);die;

            $qinshu_disease = $params['qinshu_disease'];

            foreach ($qinshu_disease as $k=>$v){
                $v = implode(',',$v);
                $qinshu_disease[$k] = $v;
            }
            $qinshu_disease['uid'] = $uid;
            //创建medical
            if ($model){
                DonorMedical::where('uid',$uid)->update($medical);
            }else{
                DonorMedical::create($medical);
            }
            //创建亲属
            foreach ($arr as $v2){
                $where_qiushu = [
                    'uid'=>$uid,
                    'type'=>$v2['type'],
                    'number'=>$v2['number'],
                ];
                $qinshu_model = DonorQinshu::where($where_qiushu)->find();
                if ($qinshu_model){
                    DonorQinshu::where($where_qiushu)->update($v2);
                }else{
                    DonorQinshu::create($v2);
                }
            }
            //创建亲属疾病
            $disease_model = DonorQinshuDisease::where('uid',$uid)->find();
            if ($disease_model){
                DonorQinshuDisease::where('uid',$uid)->update($qinshu_disease);
            }else{
                DonorQinshuDisease::create($qinshu_disease);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("row_qinshu", $row_qinshu);
        $this->view->assign("row_qinshu_disease", $row_qinshu_disease);
        $this->view->assign("number", $number);

        return $this->view->fetch();
    }

    //审核页面
    public function examine ($ids){
        $this->view->engine->layout(false);
        $user = $this->model->get(['id' => $ids]);
        //审核
        $examine = DonorExamine::where('uid',$ids)->find();
        $examine = $examine?$examine->toArray():$examine;

        //pre_screen
        $pre_screen = DonorPreScreen::where('uid',$ids)->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;

        //photo
        $donor_photo = DonorPhotos::where('uid',$ids)->find();
        $donor_photo = $donor_photo?$donor_photo->toArray():$donor_photo;


        //personal
        $personal = DonorPersonal::where('uid',$ids)->find();
        $personal = $personal?$personal->toArray():$personal;

        //education
        $education = DonorEducation::where('uid',$ids)->find();
        $education = $education?$education->toArray():$education;

        //character
        $character = DonorCharacter::where('uid',$ids)->find();
        $character = $character?$character->toArray():$character;

        //medical
        $medical = DonorMedical::where('uid',$ids)->find();
        $medical = $medical?$medical->toArray():$medical;
        if ($medical){
            if ($medical['any_diseases']){
                $medical['any_diseases'] = explode(',',$medical['any_diseases']);
            }else{
                $medical['any_diseases'] = array();
            }
        }
        $medical_qinshu = DonorQinshu::where('uid',$ids)->select();

        $number = DonorQinshu::where('uid',$ids)->where('type',7)->max('number');
//        dump($number);die;

        $medical_qinshu_disease = DonorQinshuDisease::where('uid',$ids)->find();
        $medical_qinshu_disease = $medical_qinshu_disease?$medical_qinshu_disease->toArray():'';
        if ($medical_qinshu_disease){
            foreach ($medical_qinshu_disease as $k=>$v){
                if ($k == 'id' || $k == 'uid'|| $k == 'createtime'|| $k == 'updatetime'|| $k == 'deletetime'){
                    $medical_qinshu_disease[$k] = $v;
                }else{
                    $a = explode(',',$v);
                    $medical_qinshu_disease[$k] = $a;
                }
            }
        }

        $this->view->assign('examine',$examine);
        $this->view->assign('pre_screen',$pre_screen);
        $this->view->assign('donor_photo',$donor_photo);
        $this->view->assign('personal',$personal);
        $this->view->assign('education',$education);
        $this->view->assign('character',$character);
        $this->view->assign('medical',$medical);
        $this->view->assign('row_qinshu',$medical_qinshu);
        $this->view->assign('row_qinshu_disease',$medical_qinshu_disease);
        $this->view->assign('number',$number);
        $this->view->assign('user',$user);
        $this->view->assign('uid',$ids);
        return $this->view->fetch();
    }

    //审核
    public function examine_status(){
        $params = input();
        $type = $params['type'];
        $uid = $params['uid'];
        //当前用户信息
        $user = $this->model->get(['id' => $uid]);
        $notes = [
            'form_id'=>$uid,
            'email'=>$user['email'],
            'admin_id'=>$this->auth->id,
            'status'=>1,
        ];

        $model =  DonorExamine::where('uid',$params['uid'])->find();
        $data['uid'] = $uid;
        if ($type==1){
            $data['pre_screen'] =1;
            $data['remarks_pre'] =null;
            $notes['content']='Pre-screening Approved';
            $notes['form'] = 'Pre-screening';

        }elseif ($type==2){
            $data['photos'] =1;
            $data['remarks_photos'] =null;
            $notes['content']='Photos Approved';
            $notes['form'] = 'Photos';

        }elseif ($type==3){
            $data['personal'] =1;
            $data['remarks_personal'] =null;
            $notes['content']='Personal Information Approved';
            $notes['form'] = 'Personal Information';

        }elseif ($type==4){
            $data['education'] =1;
            $data['remarks_education'] =null;
            $notes['content']='Donor Education Approved';
            $notes['form'] = 'Donor Education';

        }elseif ($type==5){
            $data['character'] =1;
            $data['remarks_character'] =null;

            $notes['content']='Character Approved';
            $notes['form'] = 'Character';

        } elseif ($type==6){
            $data['medical'] =1;
            $data['remarks_medical'] =null;

            $notes['content']='Medical Approved';
            $notes['form'] = 'Medical';

        }

        NotesDonor::create($notes);
        \app\admin\model\User::where('id',$uid)->setInc('follow_ups',1);
        if ($model){
            $res =  DonorExamine::where('uid',$params['uid'])->update($data);
        }else{
            $res = DonorExamine::create($data);
        }
        $red = 0;
        $model =  DonorExamine::where('uid',$params['uid'])->find();
        if ($model['pre_screen']=='1'&&$model['photos']=='1'&&$model['personal']=='1'
            &&$model['education']=='1'&& $model['character']=='1'&&$model['medical']=='1'){
            $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>1,'pass_time'=>time()]);
        }else{
            $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>3]);
        }
        return json(['code'=>1,'data'=>$res,'msg'=>'success','status'=>$red]);
    }

    //拒绝
    public function refuse_status(){
        $params = input();
        $type = $params['type'];
        $uid = $params['uid'];
        $textarea = $params['textarea'];
        $model =  DonorExamine::where('uid',$params['uid'])->find();
        $data['uid'] = $uid;

        //当前用户信息
        $user = $this->model->get(['id' => $uid]);
        $notes = [
            'form_id'=>$uid,
            'email'=>$user['email'],
            'admin_id'=>$this->auth->id,
            'status'=>2,
            'content'=>$textarea
        ];
        if ($type==1){
            $data['pre_screen'] =2;
            $data['remarks_pre'] =$textarea;
            $notes['form'] = 'Pre-screening';
        }elseif ($type==2){
            $data['photos'] =2;
            $data['remarks_photos'] =$textarea;
            $notes['form'] = 'Photos';
        }elseif ($type==3){
            $data['personal'] =2;
            $data['remarks_personal'] =$textarea;
            $notes['form'] = 'Obstetric History';
        }elseif ($type==4){
            $data['education'] =2;
            $data['remarks_education'] =$textarea;
            $notes['form'] = 'education Information';
        }elseif ($type==5){
            $data['character'] =2;
            $data['remarks_character'] =$textarea;
            $notes['form'] = 'Character';
        }elseif ($type==6){
            $data['medical'] =2;
            $data['remarks_medical'] =$textarea;
            $notes['form'] = 'Medical';
        }

        NotesDonor::create($notes);
        \app\admin\model\User::where('id',$uid)->setInc('follow_ups',1);
        if ($model){
            $res =  DonorExamine::where('uid',$params['uid'])->update($data);
        }else{
            $res = DonorExamine::create($data);
        }
        $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>2]);
        return json(['code'=>1,'data'=>$res,'msg'=>'success']);
    }

    public function notes($ids=null){
        $donor = \app\admin\model\User::get(['id' => $ids]);

        $this->model = new \app\admin\model\NotesDonor;
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = NotesDonor::where('form_id',$ids)
                ->with('admin')
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->view->assign("donor", $donor);
        $this->view->assign("ids", $ids);
        return $this->view->fetch();
    }

    public function notes_add(){

        $params = input();
        $status = $params['status'];
        $params['admin_id'] = $this->auth->id;
        $model = NotesDonor::where('form_id',$params['form_id'])->order('createtime','desc')->find();
        if ($model){
            $createtime = $model['createtime'];
            if (is_string($createtime)){
                $b = explode(' ',$createtime);
                $c = explode('-',$b[0]);
                $createtime = $c[2].'-'.$c[0].'-'.$c[1].' '.$b[1];
                $createtime = strtotime($createtime);
            }
            $time_duan = time() - $createtime;

            if ($time_duan < 300 && $model['content'] == $params['content']&&$model['status']==$status) {
                return json(['code' => 3, 'msg' => "Do not submit the same follow-up record within five minutes"]);
            }
        }

        $res = NotesDonor::create($params);
        if ($res){
            \app\admin\model\User::where('id',$params['form_id'])->setInc('follow_ups',1);
            \app\admin\model\User::where('id',$params['form_id'])->update(['examine_status'=>$status]);
            return json(['code'=>1,'msg'=>"Success",'res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>"Fail"]);
        }
    }

    //生成代母word
    public function donor_word(){
        $params = input();

        $uid = $params['ids'];
        //模板的路径，word的版本最好是docx，要不然可能会读取不了，根据自己的模板位置调整
        $path = 'uploads/word/donor.docx';
        $user = \app\admin\model\User::find($uid);
//        dump($user);die;
        //生成word路径，根据自己的目录调整
        $time = date("Ymd",time());
        $name = $user['first_name'].$user['last_name'].'-'.$uid;

        $filePath='uploads/word/'.$time.'/';

        if (!file_exists($filePath)){
            mkdir($filePath,0775);
        }
        $filePath= $filePath.$name.'.'.'docx';
        //声明一个模板对象、读取模板
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($path);
        $donor_photo = \app\admin\model\DonorPhotos::where('uid',$uid)->find();
        if($donor_photo){
            $photos = $donor_photo['full_images'];
            $photos .= ','.$donor_photo['child_images'];
            $photos = explode(',',$photos);
//            dump($photos);die;
            $img_count = count($photos);
            $templateProcessor->cloneBlock('block_name', $img_count, true, true);
            for($i=0;$i<$img_count;$i++){
                $img = $photos[$i];
                $templateProcessor->setImageValue("photo#".($i+1),[
                    'path' => './'.$img,
                    'width' => '600',
                    'height' => '600',
                    'marginTop'=> 20,
                    'marginLeft'=> 50,
                    'alignment'=>'center'
                ]);
            }
        }
        else{
            $templateProcessor->cloneBlock('block_name', 0, true, true);
        }

        $pre_screen = \app\admin\model\DonorPreScreen::where('uid',$uid)->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;
        if ($pre_screen){
            $templateProcessor->cloneBlock('pre_screen', 1, true, false);

            $height = round(($pre_screen['height_ft']*12*2.36) + ($pre_screen['height_in']*2.36)).'cm';
            $kg = round($pre_screen['weight']*0.45).'kg';
            $templateProcessor->setValue('first_name',$pre_screen['first_name']);
            $templateProcessor->setValue('state',$pre_screen['state']);
            $templateProcessor->setValue('age',$pre_screen['age']);
            $templateProcessor->setValue('height_ft',$pre_screen['height_ft']);
            $templateProcessor->setValue('height_in',$pre_screen['height_in']);
            $templateProcessor->setValue('height',$height);
            $templateProcessor->setValue('kg',$kg);
            $templateProcessor->setValue('weight',$pre_screen['weight']);
            $templateProcessor->setValue('ethnicity',$pre_screen['ethnicity']);
            $templateProcessor->setValue('blood_type',$pre_screen['blood_type']);
            $templateProcessor->setValue('place_of_birth',$pre_screen['place_of_birth']);
            $templateProcessor->setValue('highest_education',$pre_screen['highest_education']);
            $templateProcessor->setValue('occupation',$pre_screen['occupation']);
            $templateProcessor->setValue('is_donated',($pre_screen['is_donated']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_smoke',($pre_screen['is_smoke']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_drink',($pre_screen['is_drink']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_illicit_drugs',($pre_screen['is_illicit_drugs']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_any_medications',($pre_screen['is_any_medications']== 1?'Yes':'No'));
            $templateProcessor->setValue('abortion_reason',$pre_screen['abortion_reason']);
        }else{
            $templateProcessor->cloneBlock('pre_screen', 0, true, true);
        }

        $personal_info = \app\admin\model\DonorPersonal::where('uid',$uid)->find();
        $personal_info = $personal_info?$personal_info->toArray():$personal_info;
        if ($personal_info){
            $templateProcessor->cloneBlock('personal', 1, true, false);
            $templateProcessor->setValue('eye_color',$personal_info?$personal_info['eye_color']:'');
            $templateProcessor->setValue('natural_hair_type',$personal_info?$personal_info['natural_hair_type']:'');
            $templateProcessor->setValue('naturl_hair_color',$personal_info?$personal_info['naturl_hair_color']:'');
            $templateProcessor->setValue('skin_tone',$personal_info?$personal_info['skin_tone']:'');
            $templateProcessor->setValue('blood_type',$personal_info?$personal_info['blood_type']:'');
            $templateProcessor->setValue('predominant_hand',$personal_info?$personal_info['predominant_hand']:'');
            $templateProcessor->setValue('ethnicity',$personal_info?$personal_info['ethnicity']:'');
            $templateProcessor->setValue('is_dimples',($personal_info['is_dimples']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_eye_glasses',($personal_info['is_eye_glasses']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_had_braces',($personal_info['is_had_braces']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_tattoos',($personal_info['is_tattoos']== 1?'Yes':'No'));
            $templateProcessor->setValue('is_piercings',($personal_info['is_piercings']== 1?'Yes':'No'));
        }else{
            $templateProcessor->cloneBlock('personal', 0, true, true);
        }

        $education = \app\admin\model\DonorEducation::where('uid',$uid)->find();
        $education = $education?$education->toArray():$education;
        if ($personal_info){
            $templateProcessor->cloneBlock('education', 1, true, false);
            $templateProcessor->setValue('highest_education',$education?$education['highest_education']:'');
            $templateProcessor->setValue('is_university',$education?$education['is_university']:'');
            $templateProcessor->setValue('university',$education?$education['university']:'');
            $templateProcessor->setValue('what_study',$education?$education['what_study']:'');
            $templateProcessor->setValue('love_subjects',$education?$education['love_subjects']:'');
            $templateProcessor->setValue('certificates',$education?$education['certificates']:'');
            $templateProcessor->setValue('occupation',$education?$education['occupation']:'');
            $templateProcessor->setValue('is_plan_education',$education?$education['is_plan_education']:'');
            $templateProcessor->setValue('education_content',$education?$education['education_content']:'');

        }else{
            $templateProcessor->cloneBlock('education', 0, true, true);
        }

        $character = \app\admin\model\DonorCharacter::where('uid',$uid)->find();
        $character = $character?$character->toArray():$character;
        if ($personal_info){
            $templateProcessor->cloneBlock('character', 1, true, false);
            $templateProcessor->setValue('eye_color',$character?$character['character']:'');
            $templateProcessor->setValue('passionate_about',$character?$character['passionate_about']:'');
            $templateProcessor->setValue('is_athletic',$character?$character['is_athletic']:'');
            $templateProcessor->setValue('exercise_when',$character?$character['exercise_when']:'');
            $templateProcessor->setValue('exercise_what',$character?$character['exercise_what']:'');
            $templateProcessor->setValue('hobby',$character?$character['hobby']:'');
            $templateProcessor->setValue('is_artistic',$character?$character['is_artistic']:'');
            $templateProcessor->setValue('artistic_type',$character?$character['artistic_type']:'');
            $templateProcessor->setValue('play_instrument',$character?$character['play_instrument']:'');
            $templateProcessor->setValue('music_type',$character?$character['music_type']:'');
            $templateProcessor->setValue('books',$character?$character['books']:'');
            $templateProcessor->setValue('movies',$character?$character['movies']:'');
        }else{
            $templateProcessor->cloneBlock('character', 0, true, true);
        }

        $medical = \app\admin\model\DonorMedical::where('uid',$uid)->find();
        $medical = $medical?$medical->toArray():$medical;
        if ($personal_info){
            $templateProcessor->cloneBlock('medical', 1, true, false);
            $templateProcessor->setValue('is_hospital',$medical?$medical['is_hospital']:'');
            $templateProcessor->setValue('hospital_content',$medical?$medical['hospital_content']:'');
            $templateProcessor->setValue('is_surgeries',$medical?$medical['is_surgeries']:'');
            $templateProcessor->setValue('surgeries_content',$medical?$medical['surgeries_content']:'');
            $templateProcessor->setValue('any_medical_conditions',$medical?$medical['any_medical_conditions']:'');
            $templateProcessor->setValue('any_herbs',$medical?$medical['any_herbs']:'');
            $templateProcessor->setValue('any_allergies',$medical?$medical['any_allergies']:'');
            $templateProcessor->setValue('is_drink',$medical?$medical['is_drink']:'');
            $templateProcessor->setValue('drink_content',$medical?$medical['drink_content']:'');
            $templateProcessor->setValue('is_smoke',$medical?$medical['is_smoke']:'');
            $templateProcessor->setValue('smoke_content',$medical?$medical['smoke_content']:'');
            $templateProcessor->setValue('birth_control',$medical?$medical['birth_control']:'');
            $templateProcessor->setValue('is_menstrual_cycles',$medical?$medical['is_menstrual_cycles']:'');
            $templateProcessor->setValue('is_pregnant',$medical?$medical['is_pregnant']:'');

            $disease = explode(',',$medical['any_diseases']);
            for($i=1;$i<43;$i++){
                if (in_array($i,$disease)){
                    $templateProcessor->setValue($i,'✔');
                }else{
                    $templateProcessor->setValue($i,'');
                }
            }
//            $qinshu = \app\admin\model\DonorQinshu::where('uid',$uid)->column('type,age,height,weight,hair,eyes,
//            race,health,die,highest_education,career,number');
            $qinshu = \app\admin\model\DonorQinshu::where('uid',$uid)->select();
            for($ii=1;$ii<9;$ii++){
                $a = $ii*10;

                if (isset($qinshu[$ii-1])){
                    $templateProcessor->setValue('f_'.($a),$qinshu[$ii-1]['age']);
                    $templateProcessor->setValue('f_'.($a+1),$qinshu[$ii-1]['height']);
                    $templateProcessor->setValue('f_'.($a+2),$qinshu[$ii-1]['weight']);
                    $templateProcessor->setValue('f_'.($a+3),$qinshu[$ii-1]['hair']);
                    $templateProcessor->setValue('f_'.($a+4),$qinshu[$ii-1]['eyes']);
                    $templateProcessor->setValue('f_'.($a+5),$qinshu[$ii-1]['race']);
                    $templateProcessor->setValue('f_'.($a+6),$qinshu[$ii-1]['health']);
                    $templateProcessor->setValue('f_'.($a+7),$qinshu[$ii-1]['highest_education']);
                    $templateProcessor->setValue('f_'.($a+8),$qinshu[$ii-1]['career']);
                    $templateProcessor->setValue('f_'.($a+9),$qinshu[$ii-1]['die']);
                }else{
                    $templateProcessor->setValue('f_'.($a),'');
                    $templateProcessor->setValue('f_'.($a+1),'');
                    $templateProcessor->setValue('f_'.($a+2),'');
                    $templateProcessor->setValue('f_'.($a+3),'');
                    $templateProcessor->setValue('f_'.($a+4),'');
                    $templateProcessor->setValue('f_'.($a+5),'');
                    $templateProcessor->setValue('f_'.($a+6),'');
                    $templateProcessor->setValue('f_'.($a+7),'');
                    $templateProcessor->setValue('f_'.($a+8),'');
                    $templateProcessor->setValue('f_'.($a+9),'');
                }

                $qinshu_disease = \app\admin\model\DonorQinshuDisease::where('uid',$uid)->find()->toArray();
                unset($qinshu_disease['id']);
                unset($qinshu_disease['uid']);
                $arr = [];
                foreach ($qinshu_disease as $k=>$v){
                    $arr[] = $v;
                }
                for($iii=0;$iii<44;$iii++){
                    $a = $iii*10;
                    $b =explode(',',$arr[$iii]);
                    $templateProcessor->setValue('a_'.($a+1),$b[0]);
                    $templateProcessor->setValue('a_'.($a+2),$b[1]);
                    $templateProcessor->setValue('a_'.($a+3),$b[2]);
                    $templateProcessor->setValue('a_'.($a+4),$b[3]);
                }

            }

        }else{
            $templateProcessor->cloneBlock('medical', 0, true, true);
        }

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
}
