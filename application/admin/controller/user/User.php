<?php

namespace app\admin\controller\user;
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
class User extends Backend
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
        //筛选角色是员工
        $this->assignconfig("url",Config::get('site.url') );
        $uid = AuthGroupAccess::where('group_id',7)->column('uid');
        $user = \app\admin\model\Admin::order('id','asc')->where('id','in',$uid)->select();
        $this->view->assign("user", $user);

        $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
        $this->view->assign("role_id", $role_id);
    }

    /**
     * 查看
     */
//    public function index()
//    {
//
//        //设置过滤方法
//        $this->request->filter(['strip_tags', 'trim']);
//
//        if (false === $this->request->isAjax()) {
//            return $this->view->fetch();
//        }
//            //如果发送的来源是Selectpage，则转发到Selectpage
//            if ($this->request->request('keyField')) {
//                return $this->selectpage();
//            }
//
//            $filter = json_decode($this->request->get('filter'), true);
//            $op = json_decode($this->request->get("op"),true);
//            $where1 = [];
//            if (isset($filter['examine_status'])){
//                $where1['user.id'] = array('not in','1');
//                $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
//                if ($role_id==7){
//                    $where1['admin_id'] = $this->auth->id;
//                }
//            }elseif (isset($filter['bio'])){
//                $arr = explode(',',$filter['bio']);
//                foreach ($arr as $v){
//                    $key =  $v.'.status';
//                    $where_p[$key] = '1';
//                    unset($filter[$v]);
//                }
//                $wherea['prescreen.status'] = '1';
//
//                $form_ids = $this->model->with('prescreen,personal,ob,medical,about,other,photo,health,background')
//                    ->where($where_p)
//                    ->order('user.createtime','desc')->column('user.id');
//                $form_ids = implode(',',$form_ids);
//
//                unset($filter['bio']);
//                unset($op['bio']);
//                $filter['id'] = $form_ids;
//                $op['id'] = "in";
////                dump($filter);die;
//                $this->request->get(["filter"=>json_encode($filter),'op'=>json_encode($op)]);
//            }else{
//                $where1['user.id'] = array('not in','1');
//                $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
//                if ($role_id==7){
//                    $where1['admin_id'] = $this->auth->id;
//                }
//                $where1['user.examine_status'] = 3;
//            }
//
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//            $list = $this->model
//                ->with('group,admin,prescreen,personal,ob,medical,about,other,photo,health,background')
//                ->where($where)
//                ->where($where1)
//                ->order($sort, $order)
//                ->paginate($limit);
//            foreach ($list as $k => $v) {
//                $v->avatar = $v->avatar ? cdnurl($v->avatar, true) : letter_avatar($v->nickname);
//                $v->hidden(['password', 'salt']);
//                $progress = $this->form_pregress($v->id);
//                $v->forms = $progress;
//                \app\admin\model\User::where('id',$v->id)->update(['forms'=>$progress]);
//            }
//            $result = array("total" => $list->total(), "rows" => $list->items());
//            return json($result);
//
//    }

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
            $form_ids = $this->model->with('prescreen,personal,ob,medical,about,other,photo,health,background,sbp,examine')
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
            ->with('group,admin,prescreen,personal,ob,medical,about,other,photo,health,background,sbp,examine')
            ->where($where)
            ->where($where1)
            ->where('service','1')
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


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        return parent::add();
    }
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }
    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = input();
            $r = $params['row'];
            $validate = $this->validate($r, [
                'email' => 'require|unique:user,email,'.$ids,
                'mobile' => 'require|unique:user,mobile,'.$ids,
            ],[
                'email.require' => 'Email cannot be empty',
                'mobile.require' => 'Mobile cannot be empty',
                'email.unique' => 'Email already exists',
                'mobile.unique' => 'Mobile already exists',
            ]);
            if (true !== $validate) {
                $this->error($validate);
            }
            if ($r['password']){
                $salt = Random::alnum();
                $newpassword= $this->getEncryptPassword($r['password'], $salt);
                $data = [
                    'first_name'=>$r['first_name'],
                    'last_name'=>$r['last_name'],
                    'email'=>$r['email'],
                    'mobile'=>$r['mobile'],
                    'avatar'=>$r['avatar'],
                    'salt'=>$salt,
                    'password'=>$newpassword,
                    'loginfailure' => 0
                ];
            }else{
                $data = [
                    'first_name'=>$r['first_name'],
                    'last_name'=>$r['last_name'],
                    'email'=>$r['email'],
                    'mobile'=>$r['mobile'],
                    'avatar'=>$r['avatar'],
                ];
            }

            $res = \app\admin\model\User::where('id',$ids)->update($data);
            Hook::listen('user_edit_success',$ids);
            \app\admin\model\PreScreen::where('uid',$ids)->update([
                'first_name'=>$r['first_name'],
                'last_name'=>$r['last_name'],
                'email'=>$r['email'],
                'mobile'=>$r['mobile'],
            ]);
            if ($res){
                $this->success('successful');
            }
//            $this->token();
        }
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        return parent::edit($ids);
    }

    /**
     * 删除
     */
//    public function del($ids = "")
//    {
//        if (!$this->request->isPost()) {
//            $this->error(__("Invalid parameters"));
//        }
//        $ids = $ids ? $ids : $this->request->post("ids");
//        $row = $this->model->get($ids);
//        $this->modelValidate = true;
//        if (!$row) {
//            $this->error(__('No Results were found'));
//        }
//        Auth::instance()->delete($row['id']);
//        $this->success();
//    }

    /**
     * 删除
     *
     * @param $ids
     * @return void
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function del($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();

        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                $count += $item->delete();
            }
            Db::commit();
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }

    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $this->success("Ajax请求成功", null, ['id' => $ids]);
        }

        //预筛选
        $pre_screen = \app\admin\model\PreScreen::where('uid',$ids)->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;

        //personal_info
        $personal_info = \app\admin\model\PersonalInfo::where('uid',$ids)->find();
        $personal_info = $personal_info?$personal_info->toArray():$personal_info;

        //obstertric_history
        $obstertric_history = \app\admin\model\OtherInformation::where('uid',$ids)->find();
        $obstertric_history = $obstertric_history?$obstertric_history->toArray():$obstertric_history;

        //about_surrogacy
        $about_surrogacy = \app\admin\model\AboutSurrogacy::where('uid',$ids)->find();
        $about_surrogacy = $about_surrogacy?$about_surrogacy->toArray():$about_surrogacy;

        $surrogate_photo = SurrogatePhoto::where('uid',$ids)->value('images');
        if ($surrogate_photo){
            $surrogate_photo = explode(',',$surrogate_photo);
        }

        $background = Background::where('uid',$ids)->find();
        $background = $background?$background:'';

        $bg_id =Background::where('uid',$ids)->value('bid');
        if ($bg_id){
            $bg_id = explode(',',$bg_id);
        }
        $this->view->assign("bg_id", $bg_id);

        $sbp = SurrogacySbp::where('uid',$ids)->find();
        $sbp = $sbp?$sbp:'';

        //代母孩子
        $surrogate_baby = SurrogateBaby::where('uid',$ids)->select();
        $surrogate_baby = $surrogate_baby?$surrogate_baby:'';
//        dump($surrogate_baby);die;

        //医疗记录
        $regnancy_information = RegnancyInformation::where('uid',$ids)->select();
        $regnancy_information = $regnancy_information?$regnancy_information:'';

        //审核
        $examine = Examine::where('uid',$ids)->find();

        $this->view->assign("surrogate_photo", $surrogate_photo);
        $this->view->assign("regnancy_information", $regnancy_information);
        $this->view->assign("row", $row->toArray());
        $this->view->assign("ids", $ids);
        $this->view->assign("pre_screen", $pre_screen);
        $this->view->assign("personal_info", $personal_info);
        $this->view->assign("obstertric_history", $obstertric_history);
        $this->view->assign("about_surrogacy", $about_surrogacy);
        $this->view->assign("background", $background);
        $this->view->assign("sbp", $sbp);
        $this->view->assign("surrogate_baby", $surrogate_baby);
        $this->view->assign("examine", $examine);
        $this->view->engine->layout(false);


        //form
        $uid = $ids;
        $form_number_list = FormNumber::order('id','asc')->column('id,name,number');

        //pre_screen
        $form_pre_screen_complate = PreScreen::where('uid',$uid)->value('form_complate_number');
        $form_pre_screen_lv = $form_pre_screen_complate/$form_number_list[1]['number']*100;

        //presonal_info
        $form_presonal_info_complate = PersonalInfo::where('uid',$uid)->value('form_complate_number');
        $form_presonal_info_lv = $form_presonal_info_complate/$form_number_list[2]['number']*100;

        //obstertric_history
        $form_obstertric_history_complate = ObstetricHistory::where('uid',$uid)->value('form_complate_number');
        $form_obstertric_history_lv = $form_obstertric_history_complate/$form_number_list[3]['number']*100;

        //medical_information
        $form_medical_information_complate = MedicalInformation::where('uid',$uid)->value('form_complate_number');
        $form_medical_information_lv = $form_medical_information_complate/$form_number_list[4]['number']*100;

        //about_surrogacy
        $form_about_surrogacy_complate = AboutSurrogacy::where('uid',$uid)->value('form_complate_number');
        $form_about_surrogacy_lv = $form_about_surrogacy_complate/$form_number_list[5]['number']*100;

        //other_information
        $form_other_information_complate = OtherInformation::where('uid',$uid)->value('form_complate_number');
        $form_other_information_lv = $form_other_information_complate/$form_number_list[6]['number']*100;

        //photots
        $form_photos_complate = SurrogatePhoto::where('uid',$uid)->value('form_complate_number');
        $form_photos_lv = $form_photos_complate/$form_number_list[7]['number']*100;

        //background
        $form_background_complate = Background::where('uid',$uid)->value('form_complate_number');
        $form_background_lv = $form_background_complate/($form_number_list[9]['number'])*100;
        //sbp
        $form_sbp_complate = SurrogacySbp::where('uid',$uid)->value('form_complate_number');
        $form_sbp_lv = $form_sbp_complate/$form_number_list[10]['number']*100;

        //health_record_release
        $form_health_record_release_complate = HealthRecordRelease::where('uid',$uid)->value('form_complate_number');
        $form_health_record_release_lv = $form_health_record_release_complate/$form_number_list[8]['number']*100;

        //
        $medical_fax_model =RegnancyInformation::where('uid',$uid)->select();
        $medical_fax_number = count($medical_fax_model)*2;
        $form_medical_complate = 0;
        if ($medical_fax_model){
            foreach ($medical_fax_model as $v){
                if ($v['ob_file']){
                    $form_medical_complate++;
                }
                if ($v['delivery_file']){
                    $form_medical_complate++;
                }
            }
        }
        if ($medical_fax_number){
            $form_medical_fax_lv = $form_medical_complate/$medical_fax_number*100;
        }else{

            $form_medical_fax_lv = 0;
        }

        $this->view->assign('medical_fax_number',$medical_fax_number);
        $this->view->assign('form_medical_complate',$form_medical_complate);
        $this->view->assign('form_medical_fax_lv',$form_medical_fax_lv);

//        $step = 0;
//        if ($form_pre_screen_lv==100&&$form_presonal_info_lv==100&&$form_obstertric_history_lv==100&&$form_medical_information_lv==100
//            &&$form_about_surrogacy_lv==100&&$form_other_information_lv==100&&$form_photos_lv==100
//            &&$form_health_record_release_lv==100){
//            $step = 1;
//        }
//        $this->view->assign('step', $step);

        $this->view->assign('form_number_list', $form_number_list);
        $this->view->assign('form_pre_screen_complate', $form_pre_screen_complate?$form_pre_screen_complate:0);
        $this->view->assign('form_pre_screen_lv', $form_pre_screen_lv);
        $this->view->assign('form_presonal_info_complate', $form_presonal_info_complate?$form_presonal_info_complate:0);
        $this->view->assign('form_presonal_info_lv', $form_presonal_info_lv);
        $this->view->assign('form_obstertric_history_complate', $form_obstertric_history_complate?$form_obstertric_history_complate:0);
        $this->view->assign('form_obstertric_history_lv',$form_obstertric_history_lv);
        $this->view->assign('form_medical_information_complate', $form_medical_information_complate?$form_medical_information_complate:0);
        $this->view->assign('form_medical_information_lv',$form_medical_information_lv);
        $this->view->assign('form_about_surrogacy_complate', $form_about_surrogacy_complate?$form_about_surrogacy_complate:0);
        $this->view->assign('form_about_surrogacy_lv',$form_about_surrogacy_lv);
        $this->view->assign('form_other_information_complate', $form_other_information_complate?$form_other_information_complate:0);
        $this->view->assign('form_other_information_lv',$form_other_information_lv);
        $this->view->assign('form_photos_complate', $form_photos_complate?$form_photos_complate:0);
        $this->view->assign('form_photos_lv',$form_photos_lv);
        $this->view->assign('form_background_complate', $form_background_complate?$form_background_complate:0);
        $this->view->assign('form_background_lv',$form_background_lv);
        $this->view->assign('form_sbp_complate', $form_sbp_complate?$form_sbp_complate:0);
        $this->view->assign('form_sbp_lv',$form_sbp_lv);
        $this->view->assign('form_health_record_release_complate', $form_health_record_release_complate?$form_health_record_release_complate:0);
        $this->view->assign('form_health_record_release_lv',$form_health_record_release_lv);



        return $this->view->fetch();
    }


//    public function detail($ids)
//    {
//        $row = $this->model->get(['id' => $ids]);
//        if (!$row) {
//            $this->error(__('No Results were found'));
//        }
//        if ($this->request->isAjax()) {
//            $this->success("Ajax请求成功", null, ['id' => $ids]);
//        }
//
//        //预筛选
//        $pre_screen = \app\admin\model\PreScreen::where('uid',$ids)->find();
//        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;
//
//        //personal_info
//        $personal_info = \app\admin\model\PersonalInfo::where('uid',$ids)->find();
//        $personal_info = $personal_info?$personal_info->toArray():$personal_info;
//
//        //obstertric_history
//        $obstertric_history = \app\admin\model\OtherInformation::where('uid',$ids)->find();
//        $obstertric_history = $obstertric_history?$obstertric_history->toArray():$obstertric_history;
//
//        //about_surrogacy
//        $about_surrogacy = \app\admin\model\AboutSurrogacy::where('uid',$ids)->find();
//        $about_surrogacy = $about_surrogacy?$about_surrogacy->toArray():$about_surrogacy;
//
//        $surrogate_photo = SurrogatePhoto::where('uid',$ids)->value('images');
//        if ($surrogate_photo){
//            $surrogate_photo = explode(',',$surrogate_photo);
//        }
//
//        $background = Background::where('uid',$ids)->find();
//        $background = $background?$background:'';
//
//        $sbp = SurrogacySbp::where('uid',$ids)->find();
//        $sbp = $sbp?$sbp:'';
//
//        //代母孩子
//        $surrogate_baby = SurrogateBaby::where('uid',$ids)->select();
//        $surrogate_baby = $surrogate_baby?$surrogate_baby:'';
////        dump($surrogate_baby);die;
//
//        //医疗记录
//        $regnancy_information = RegnancyInformation::where('uid',$ids)->select();
//        $regnancy_information = $regnancy_information?$regnancy_information:'';
//
//        //审核
//        $examine = Examine::where('uid',$ids)->find();
//
//        $this->view->assign("surrogate_photo", $surrogate_photo);
//        $this->view->assign("regnancy_information", $regnancy_information);
//        $this->view->assign("row", $row->toArray());
//        $this->view->assign("ids", $ids);
//        $this->view->assign("pre_screen", $pre_screen);
//        $this->view->assign("personal_info", $personal_info);
//        $this->view->assign("obstertric_history", $obstertric_history);
//        $this->view->assign("about_surrogacy", $about_surrogacy);
//        $this->view->assign("background", $background);
//        $this->view->assign("sbp", $sbp);
//        $this->view->assign("surrogate_baby", $surrogate_baby);
//        $this->view->assign("examine", $examine);
//        $this->view->engine->layout(false);
//
//
//        //form
//        $uid = $ids;
//        $form_number_list = FormNumber::order('id','asc')->column('id,name,number');
//
//        //pre_screen
//        $form_pre_screen_complate = PreScreen::where('uid',$uid)->value('form_complate_number');
//        $form_pre_screen_lv = $form_pre_screen_complate/$form_number_list[1]['number']*100;
//
//        //presonal_info
//        $form_presonal_info_complate = PersonalInfo::where('uid',$uid)->value('form_complate_number');
//        $form_presonal_info_lv = $form_presonal_info_complate/$form_number_list[2]['number']*100;
//
//        //obstertric_history
//        $form_obstertric_history_complate = ObstetricHistory::where('uid',$uid)->value('form_complate_number');
//        $form_obstertric_history_lv = $form_obstertric_history_complate/$form_number_list[3]['number']*100;
//
//        //medical_information
//        $form_medical_information_complate = MedicalInformation::where('uid',$uid)->value('form_complate_number');
//        $form_medical_information_lv = $form_medical_information_complate/$form_number_list[4]['number']*100;
//
//        //about_surrogacy
//        $form_about_surrogacy_complate = AboutSurrogacy::where('uid',$uid)->value('form_complate_number');
//        $form_about_surrogacy_lv = $form_about_surrogacy_complate/$form_number_list[5]['number']*100;
//
//        //other_information
//        $form_other_information_complate = OtherInformation::where('uid',$uid)->value('form_complate_number');
//        $form_other_information_lv = $form_other_information_complate/$form_number_list[6]['number']*100;
//
//        //photots
//        $form_photos_complate = SurrogatePhoto::where('uid',$uid)->value('form_complate_number');
//        $form_photos_lv = $form_photos_complate/$form_number_list[7]['number']*100;
//
//        //background
//        $form_background_complate = Background::where('uid',$uid)->value('form_complate_number');
//        $form_background_lv = $form_background_complate/$form_number_list[8]['number']*100;
//
//
//        //health_record_release
//        $form_health_record_release_complate = HealthRecordRelease::where('uid',$uid)->value('form_complate_number');
//        $form_health_record_release_lv = $form_health_record_release_complate/$form_number_list[8]['number']*100;
//
//        $step = 0;
//        if ($form_pre_screen_lv==100&&$form_presonal_info_lv==100&&$form_obstertric_history_lv==100&&$form_medical_information_lv==100
//            &&$form_about_surrogacy_lv==100&&$form_other_information_lv==100&&$form_photos_lv==100
//            &&$form_health_record_release_lv==100){
//            $step = 1;
//        }
//        $this->view->assign('step', $step);
//
//        $this->view->assign('form_number_list', $form_number_list);
//        $this->view->assign('form_pre_screen_complate', $form_pre_screen_complate?$form_pre_screen_complate:0);
//        $this->view->assign('form_pre_screen_lv', $form_pre_screen_lv);
//        $this->view->assign('form_presonal_info_complate', $form_presonal_info_complate?$form_presonal_info_complate:0);
//        $this->view->assign('form_presonal_info_lv', $form_presonal_info_lv);
//        $this->view->assign('form_obstertric_history_complate', $form_obstertric_history_complate?$form_obstertric_history_complate:0);
//        $this->view->assign('form_obstertric_history_lv',$form_obstertric_history_lv);
//        $this->view->assign('form_medical_information_complate', $form_medical_information_complate?$form_medical_information_complate:0);
//        $this->view->assign('form_medical_information_lv',$form_medical_information_lv);
//        $this->view->assign('form_about_surrogacy_complate', $form_about_surrogacy_complate?$form_about_surrogacy_complate:0);
//        $this->view->assign('form_about_surrogacy_lv',$form_about_surrogacy_lv);
//        $this->view->assign('form_other_information_complate', $form_other_information_complate?$form_other_information_complate:0);
//        $this->view->assign('form_other_information_lv',$form_other_information_lv);
//        $this->view->assign('form_photos_complate', $form_photos_complate?$form_photos_complate:0);
//        $this->view->assign('form_photos_lv',$form_photos_lv);
//        $this->view->assign('form_background_complate', $form_background_complate?$form_background_complate:0);
//        $this->view->assign('form_background_lv',$form_background_lv);
//        $this->view->assign('form_health_record_release_complate', $form_health_record_release_complate?$form_health_record_release_complate:0);
//        $this->view->assign('form_health_record_release_lv',$form_health_record_release_lv);
//
//
//
//        return $this->view->fetch();
//    }

    public function background($ids){
        $params['file'] = input('file');
        $params['uid'] = $ids;
        $params['bid'] = input('bid');
        if (!$params['file']||!$params['bid']){
            $this->error('File upload cannot be empty');
        }
        $params['status'] = '1';
        $model = Background::where('uid',$ids)->find();
        if ($model){
            Background::where('uid',$ids)->update($params);
        }else{
            Background::create($params);
        }
        $this->success('successful');
    }

    public function sbp($ids){
        $params['file'] = input('file');
        $params['uid'] = $ids;
        if (!$params['file']){
            $this->error('File upload cannot be empty');
        }
        $params['status'] = '1';
        $model = SurrogacySbp::where('uid',$ids)->find();
        if ($model){
            SurrogacySbp::where('uid',$ids)->update($params);
        }else{
            SurrogacySbp::create($params);
        }
        $this->success('successful');
    }

    /**
     * 审核
     */
    public function shenhe(){
        $params = input();
        $res = \app\admin\model\User::where('id',$params['id'])
            ->update(['examine_status'=>$params['examine_status'],'shenhe_content'=>$params['content']]);
        return json(['code'=>1,'data'=>$res,'msg'=>'success']);
    }

    public function form_pregress($uid=null){

        //pre_screen
        $form_pre_screen_complate = PreScreen::where('uid',$uid)->value('status');
        $form_pre_screen_complate = $form_pre_screen_complate?(int)$form_pre_screen_complate:0;

        //presonal_info
        $form_presonal_info_complate = PersonalInfo::where('uid',$uid)->value('status');
        $form_presonal_info_complate = $form_presonal_info_complate?(int)$form_presonal_info_complate:0;

        //obstertric_history
        $form_obstertric_history_complate = ObstetricHistory::where('uid',$uid)->value('status');
        $form_obstertric_history_complate = $form_obstertric_history_complate?(int)$form_obstertric_history_complate:0;

        //medical_information
        $form_medical_information_complate = MedicalInformation::where('uid',$uid)->value('status');
        $form_medical_information_complate = $form_medical_information_complate?(int)$form_medical_information_complate:0;

        //about_surrogacy
        $form_about_surrogacy_complate = AboutSurrogacy::where('uid',$uid)->value('status');
        $form_about_surrogacy_complate = $form_about_surrogacy_complate?(int)$form_about_surrogacy_complate:0;

        //other_information
        $form_other_information_complate = OtherInformation::where('uid',$uid)->value('status');
        $form_other_information_complate = $form_other_information_complate?(int)$form_other_information_complate:0;

        //photots
        $form_photos_complate = SurrogatePhoto::where('uid',$uid)->value('status');
        $form_photos_complate = $form_photos_complate?(int)$form_photos_complate:0;

//        //additional_information
//        $form_additional_information_complate = AdditionalInformation::where('uid',$uid)->value('status');
//        $form_additional_information_complate = $form_additional_information_complate?(int)$form_additional_information_complate:0;

        //health_record_release
        $form_health_record_release_complate = HealthRecordRelease::where('uid',$uid)->value('status');
        $form_health_record_release_complate = $form_health_record_release_complate?(int)$form_health_record_release_complate:0;

        $step = 0;
        $step = ($form_pre_screen_complate+$form_presonal_info_complate+$form_obstertric_history_complate+$form_medical_information_complate
                +$form_about_surrogacy_complate+$form_other_information_complate+$form_photos_complate+
                $form_health_record_release_complate)/8*100;
        return intval($step);

    }

//xiaobao
    public function examine ($ids){
        $this->view->engine->layout(false);
        $row = $this->model->get(['id' => $ids]);


        //预筛选
        $pre_screen = \app\admin\model\PreScreen::where('uid',$ids)->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;
        if ($pre_screen&&$pre_screen['birthday_time']){
            $pre_screen['birthday_time'] = $this->time($pre_screen['birthday_time']);
        }

        //personal_info
        $personal_info = \app\admin\model\PersonalInfo::where('uid',$ids)->find();
        $personal_info = $personal_info?$personal_info->toArray():$personal_info;

        //obstertric_history
        $obstertric_history = ObstetricHistory::where('uid',$ids)->find();
        $obstertric_history = $obstertric_history?$obstertric_history->toArray():$obstertric_history;
        if ($obstertric_history&&$obstertric_history['last_period_time']){
            $obstertric_history['last_period_time'] = $this->time($obstertric_history['last_period_time']);
        }
        //代母孩子
        $surrogate_baby = SurrogateBaby::where('uid',$ids)->select();
        $surrogate_baby = $surrogate_baby?$surrogate_baby:'';
        $button_number = '';
        if ($surrogate_baby){
            foreach ($surrogate_baby as $k=>$v){
                $a = $this->time($v['birthday_time']);
//                $surrogate_baby[$k]['birthday_time']=$a ;
                $surrogate_baby[$k]['bir_time']=$a ;
            }
            $button_number = count($surrogate_baby);

        }
        $this->view->assign('button_number',$button_number);


        $medical_information = \app\admin\model\MedicalInformation::where('uid',$ids)->find();
        $medical_information = $medical_information?$medical_information->toArray():$medical_information;
        if ($medical_information){
            $medical_information['any_diseases'] = explode(',',$medical_information['any_diseases']);
        }

        //about_surrogacy
        $about_surrogacy = \app\admin\model\AboutSurrogacy::where('uid',$ids)->find();
        $about_surrogacy = $about_surrogacy?$about_surrogacy->toArray():$about_surrogacy;
        if ($about_surrogacy){
            $about_surrogacy['parents_type'] = explode(',',$about_surrogacy['parents_type']);
        }

        //other_information
        $other_information = \app\admin\model\OtherInformation::where('uid',$ids)->find();
        $other_information = $other_information?$other_information->toArray():$other_information;
        $this->view->assign('other_information',$other_information);

        //background
        $background = \app\admin\model\Background::where('uid',$ids)->find();
        $background = $background?$background->toArray():$background;
        $this->view->assign('background',$background);

        $surrogate_photo = SurrogatePhoto::where('uid',$ids)->find();
        $surrogate_photo = $surrogate_photo?$surrogate_photo:'';


        $additional_information = AdditionalInformation::where('uid',$ids)->find();
        $additional_information = $additional_information?$additional_information->toArray():$additional_information;

        $health = HealthRecordRelease::where('uid',$ids)->find();
        $health = $health?$health->toArray():$health;
//        if ($health){
//            $health['canvas_time_a'] = $this->time($health['canvas_time']);
//            $health['name_time_a'] = $this->time($health['name_time']);
//        }

        $regnancy_informatiom_model = RegnancyInformation::where('uid',$ids)->order('number','asc')->select();
        $regnancy_informatiom_data = [];
        $button_number_re = '';
        if ($regnancy_informatiom_model){
            foreach ($regnancy_informatiom_model as $k=>$v){
                $regnancy_informatiom_data[] = $v;
            }
            foreach ($regnancy_informatiom_data as $k=>$v){
                if ($v['delivery_date']){
                    $regnancy_informatiom_data[$k]['delivery_date'] = $this->time($v['delivery_date']);
                }

            }
            $button_number_re = count($regnancy_informatiom_data);
        }
        $this->view->assign('regnancy_informatiom_data',$regnancy_informatiom_data);
        $this->view->assign('button_number_re',$button_number_re);


        //审核
        $examine = Examine::where('uid',$ids)->find();
        $examine = $examine?$examine->toArray():$examine;


        $this->view->assign('row',$row);
        $this->view->assign('ids',$ids);
        $this->view->assign('pre_screen',$pre_screen);
        $this->view->assign('personal_info',$personal_info);
        $this->view->assign('obstertric_history',$obstertric_history);
        $this->view->assign('about_surrogacy',$about_surrogacy);
        $this->view->assign('medical_information',$medical_information);
        $this->view->assign('examine',$examine);
        $this->view->assign('surrogate_baby',$surrogate_baby);
        $this->view->assign('surrogate_photo',$surrogate_photo);
        $this->view->assign('additional_information',$additional_information);
        $this->view->assign('health',$health);

        return $this->view->fetch();
    }

    public function ob_record_file($ids){

        $file = input('ob_file');
        if (!$file){
            $this->error('File upload cannot be empty');
        }
        $model = RegnancyInformation::where('id',$ids)->find();
        if ($model){
            RegnancyInformation::where('id',$ids)->update(['ob_file'=>$file]);
        }else{
            $this->error('Model does not exist');
        }
        $this->success('successful');
    }

    public function delivery_record_file($ids){

        $file = input('delivery_file');
        if (!$file){
            $this->error('File upload cannot be empty');
        }
        $model = RegnancyInformation::where('id',$ids)->find();
        if ($model){
            RegnancyInformation::where('id',$ids)->update(['delivery_file'=>$file]);
        }else{
            $this->error('Model does not exist');
        }
        $this->success('successful');
    }


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

        $model =  Examine::where('uid',$params['uid'])->find();
        $data['uid'] = $uid;
        if ($type==1){
            $data['pre_screen'] =1;
            $data['remarks_pre'] =null;
            $notes['content']='Pre-screening Approved';
            $notes['form'] = 'Pre-screening';

        }elseif ($type==2){
            $data['personal_info'] =1;
            $data['remarks_info'] =null;
            $notes['content']='Personal Information Approved';
            $notes['form'] = 'Personal Information';

        }elseif ($type==3){
            $data['ob'] =1;
            $data['remarks_ob'] =null;

            $notes['content']='Obstetric History Approved';
            $notes['form'] = 'Obstetric History';

        }elseif ($type==4){
            $data['medical'] =1;
            $data['remarks_medical'] =null;

            $notes['content']='Medical Information Approved';
            $notes['form'] = 'Medical Information';

        }elseif ($type==5){
            $data['about'] =1;
            $data['remarks_about'] =null;

            $notes['content']='About Surrogacy Approved';
            $notes['form'] = 'About Surrogacy';

        }elseif ($type==6){
            $data['other'] =1;
            $data['remarks_other'] =null;

            $notes['content']='Other Information Approved';
            $notes['form'] = 'Other Information';

        }elseif ($type==7){
            $data['photos'] =1;
            $data['remarks_photos'] =null;

            $notes['content']='Photos Approved';
            $notes['form'] = 'Photos';

        }elseif ($type==8){
            $data['health'] =1;
            $data['remarks_health'] =null;

            $notes['content']='Health Record Release Approved';
            $notes['form'] = 'Health Record Release';

        } elseif ($type==9){
            $data['background'] =1;
            $data['remarks_background'] =null;
            $notes['content']='Background Approved';
            $notes['form'] = 'Background';
        }
        NotesSurrogacy::create($notes);
        \app\admin\model\User::where('id',$uid)->setInc('follow_ups',1);
        if ($model){
            $res =  Examine::where('uid',$params['uid'])->update($data);
        }else{
            $res = Examine::create($data);
        }
//       if ($model['pre_screen']=='1'||$model['personal_info']=='1'||$model['ob']=='1'||$model['medical']=='1'||
//            $model['about']=='1'||$model['other']=='1'||$model['photos']=='1'||$model['health']=='1'){
//            $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>3]);
//        }
        $red = 0;
        $model =  Examine::where('uid',$params['uid'])->find();
        if ($model['pre_screen']=='1'&&$model['personal_info']=='1'&&$model['ob']=='1'&&$model['medical']=='1'&&
            $model['about']=='1'&&$model['other']=='1'&&$model['photos']=='1'&&$model['health']=='1'&&$model['background']=='1'){
            $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>1,'pass_time'=>time()]);
        }else{
            $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>3]);
        }
        return json(['code'=>1,'data'=>$res,'msg'=>'success','status'=>$red]);
    }

    public function refuse_status(){
        $params = input();
        $type = $params['type'];
        $uid = $params['uid'];
        $textarea = $params['textarea'];
        $model =  Examine::where('uid',$params['uid'])->find();
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
            $data['personal_info'] =2;
            $data['remarks_info'] =$textarea;
            $notes['form'] = 'Personal Information';
        }elseif ($type==3){
            $data['ob'] =2;
            $data['remarks_ob'] =$textarea;
            $notes['form'] = 'Obstetric History';
        }elseif ($type==4){
            $data['medical'] =2;
            $data['remarks_medical'] =$textarea;
            $notes['form'] = 'Medical Information';
        }elseif ($type==5){
            $data['about'] =2;
            $data['remarks_about'] =$textarea;
            $notes['form'] = 'About Surrogacy';
        }elseif ($type==6){
            $data['other'] =2;
            $data['remarks_other'] =$textarea;
            $notes['form'] = 'Other Information';
        }elseif ($type==7){
            $data['photos'] =2;
            $data['remarks_photos'] =$textarea;
            $notes['form'] = 'Photos';
        }elseif ($type==8){
            $data['health'] =2;
            $data['remarks_health'] =$textarea;
            $notes['form']='Health Record Release';
        }elseif ($type==9){
            $data['background'] =2;
            $data['remarks_background'] =$textarea;
            $notes['form']='background';
        }elseif ($type==10){
            $data['sbp'] =2;
            $data['remarks_sbp'] =$textarea;
            $notes['form']='SBP';
        }
        NotesSurrogacy::create($notes);
        \app\admin\model\User::where('id',$uid)->setInc('follow_ups',1);
        if ($model){
            $res =  Examine::where('uid',$params['uid'])->update($data);
        }else{
            $res = Examine::create($data);
        }
        $red = \app\admin\model\User::where('id',$uid)->update(['examine_status'=>2]);
        return json(['code'=>1,'data'=>$res,'msg'=>'success']);
    }


    //生成Word文档
    public function word(){
        $params = input();
//        dump($params);die;
        //模板的路径，word的版本最好是docx，要不然可能会读取不了，根据自己的模板位置调整
        $type = $params['type'];
        $user = \app\admin\model\User::find($params['uid']);
        if ($type==1){
            $path = 'uploads/fax/ob_records.docx';
            $name = 'ob-'.$user['first_name'].$user['last_name'].'-'.$params['id'];
        }else{
            $path = 'uploads/fax/delivery_records.docx';
            $name = 'delivery-'.$user['first_name'].$user['last_name'].'-'.$params['id'];
        }
//        dump($user);die;
        //生成word路径，根据自己的目录调整
        $time = date("Ymd",time());
        $filePath='uploads/fax/'.$time.'/';
        if (!file_exists($filePath)){
            mkdir($filePath,0775);
        }
        $filePath= $filePath.$name.'.'.'docx';

        //声明一个模板对象、读取模板
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($path);

        //替换模板中的变量，对应word里的 ${test}
        $hospital = RegnancyInformation::find($params['id']);
//        $health = HealthRecordRelease::where('uid',$params['uid'])->find();
        $pre_screen = PreScreen::where('uid',$params['uid'])->find();


        $ob = '';
        if ($pre_screen){
            $ob = $pre_screen['birthday_time'];
            $ob = date('m-d-Y',$ob);
        }
        $templateProcessor->setValue('date',date('m-d-Y',time()));
        $templateProcessor->setValue('username',$user['first_name'].' '.$user['last_name']);
        $templateProcessor->setValue('ob',$ob);
        $templateProcessor->setValue('phone',$user['mobile']);
//        dump($ob);die;
        if ($type==1){
            $templateProcessor->setValue('doctor',$hospital['doctor']);
            $templateProcessor->setValue('d_phone',$hospital['hospital_phone']);
            $templateProcessor->setValue('d_fax',$hospital['hospital_fax']);
            $templateProcessor->setValue('d_address',$hospital['address_one']);

        }elseif($type==2){
            $templateProcessor->setValue('hospital',$hospital['delivery_hospital_name']);
            $templateProcessor->setValue('h_phone',$hospital['hospital_phone_two']);
            $templateProcessor->setValue('h_fax',$hospital['hospital_fax_two']);
            $templateProcessor->setValue('h_address',$hospital['address_line']);
        }
        //生成新的word
        $templateProcessor->saveAs($filePath);
        if(file_exists($filePath))
        {
//            RegnancyInformation::where('id',$params['id'])->update(['file'=>$filePath]);
            return json(['code'=>1,'data'=>$filePath,'msg'=>'success']);
        }
        else
        {
            return json(['code'=>2,'data'=>$filePath,'msg'=>'fail']);
        }
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

    public function fenpei(){
        $params = input();

        $admin_id = $params['category'];
        $form_ids = $params['ids'];
        $form_ids = explode(',',$form_ids);
        foreach ($form_ids as $v){
            $model = $this->model->find($v);
//            if ($model['admin_id']){
//                return json(['code'=>3,'msg'=>'There are already followers, please do not reassign','res'=>$model]);
//            }
            $res = $this->model->where('id',$v)->update(['admin_id'=>$admin_id]);
        }
        if ($res){
            return json(['code'=>1,'msg'=>'Assigned successfully','res'=>$res]);
        }else{
            return json(['code'=>0,'msg'=>'allocation failure']);
        }

    }

    public function upload(){
        $params = input();
        $file = $params['file'];
        $form_ids = $params['ids'];
        $form_ids = explode(',',$form_ids);
        foreach ($form_ids as $v){
            $res = $this->model->where('id',$v)->update(['examine_file'=>$file,'examine_status'=>1]);
        }
        if ($res){
            return json(['code'=>1,'msg'=>'Upload success','res'=>$res]);
        }else{
            return json(['code'=>0,'msg'=>'Upload fail']);
        }

    }

    public function surrogacy_progress(){
        $params = input();
        $surrogacy_progress = $params['progress'];
        $form_ids = $params['ids'];
        $form_ids = explode(',',$form_ids);
        foreach ($form_ids as $v){
            $res = $this->model->where('id',$v)->update(['surrogacy_progress'=>$surrogacy_progress]);
        }
        if ($res){
            return json(['code'=>1,'msg'=>'Add success','res'=>$res]);
        }else{
            return json(['code'=>0,'msg'=>'Add fail']);
        }
    }

    //生成代母pdf
    public function pdf(){
//        $uid = 61;
        $params = input();
        $uid = $params['ids'];
        $type = $params['type'];

        $user = \app\admin\model\User::find($uid);
        if($user['forms']==0){
            return json(['code'=>2,'msg'=>'Form is empty and cannot be exported']);
        }

        //预筛选
        $pre_screen = \app\admin\model\PreScreen::where('uid',$uid)->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;
        if ($pre_screen){
            $pre_screen['height'] = round(($pre_screen['height_ft']*12*2.36) + ($pre_screen['height_in']*2.36));
        }

        //personal_info
        $personal_info = \app\admin\model\PersonalInfo::where('uid',$uid)->find();
        $personal_info = $personal_info?$personal_info->toArray():$personal_info;

        //obstertric_history
        $obstertric_history = \app\admin\model\ObstetricHistory::where('uid',$uid)->find();
        $obstertric_history = $obstertric_history?$obstertric_history->toArray():$obstertric_history;


        //medical information
        $medical_information = \app\admin\model\MedicalInformation::where('uid',$uid)->find();
        $medical_information = $medical_information?$medical_information->toArray():$medical_information;

        //about_surrogacy
        $about_surrogacy = \app\admin\model\AboutSurrogacy::where('uid',$uid)->find();
        $about_surrogacy = $about_surrogacy?$about_surrogacy->toArray():$about_surrogacy;

        $other_information = \app\admin\model\OtherInformation::where('uid',$uid)->find();
        $other_information = $other_information?$other_information->toArray():$other_information;


        $surrogate_photo = \app\admin\model\SurrogatePhoto::where('uid',$uid)->value('images');
        if ($surrogate_photo){
            $surrogate_photo = explode(',',$surrogate_photo);
        }

        $surrogate_baby = \app\admin\model\SurrogateBaby::where('uid',$uid)->select();
        $surrogate_baby = $surrogate_baby?$surrogate_baby:'';


        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => __DIR__ . '/tmp',
        ]);
        $mpdf->autoLangToFont = true;
        $mpdf->autoScriptToLang = true;

        //默认 以html为标准分析写入内容
        $html = $this->html($type,$pre_screen,$personal_info,$obstertric_history,$medical_information,$about_surrogacy,$other_information,$surrogate_photo,$surrogate_baby);
        $mpdf->WriteHTML($html);
        // 文件生成指令
//        $mpdf->Output();
//         $a = $this->random_code();
        $state = $pre_screen['state']?$pre_screen['state']:'';
        $a = $user['first_name'].$user['last_name']. '_' .$state;
        $file = 'uploads/pdf/'.$a.'.pdf';
        $mpdf->Output($file);
        return json(['code'=>1,'msg'=>'导出pdf成功','res'=>$file]);


    }
    public function html($type,$pre_screen,$personal_info,$obstertric_history,$medical_information,$about_surrogacy,$other_information,$surrogate_photo,$surrogate_baby){
        $html = '<!DOCTYPE html>';
        $html .='<html lang="en">';
        $html .='<head>';


        $html .='<style>
     .title {
            letter-spacing: 1px;
            border-bottom: 1px solid;
            padding: 10px;
        }
         ws_table td{
               
                border: 1px solid #000000;
                border-collapse: collapse;
            }
            .fenye {
            page-break-before: right;
            }
   </style>';
        $html .='</head>';
        $html .='<body class="landing-page">';
        $html .='<div>';
//        $html .='<div class="logo" align="center"><img src="https://vip.dopusa.com/uploads/logo.png"></div>';
        $html .='<div class="logo" align="center"><img src="./uploads/logo.png"></div>';
        $html .='<div class="content">';
        $html .='<h2 class="title" align="center">Surrogate Profile</h2>';
        if ($surrogate_photo){
            foreach ($surrogate_photo as $v){
                $html .='<div>';
                $html .='<img height="210mm" src="./'.$v.'" style="width:100%">';
                $html .='</div>';
            }
            $html .='<div style="page-break-before: right">';
        }
        else{
            $html .='<div>';
        }
        if ($pre_screen){
            if ($type==1){
                $html .='<h3>Personal Information</h3>';
                $html .='<table>';
                $html .='<tr>';
                $html .='<td style="width: 20%">First name : '.$pre_screen['first_name'].'</td>';
//            $html .='<td style="width: 20%">Last name : '.$pre_screen['last_name'].'</td>';
                $html .='<td style="width: 20%">Date of birth : '.date("m-d-Y",$pre_screen['birthday_time']).'</td>';
                $html .='</tr>';
                $html .='<tr>';
                $html .='<td style="width: 20%">City : '.$pre_screen['city'].'</td>';
                $html .='<td style="width: 20%">State : '.$pre_screen['state'].'</td>';
                $html .='<td style="width: 20%">Age : '.$pre_screen['age'].'</td>';
                $html .='</tr>';
                $html .='<tr>';
                $html .='<td style="width: 20%">Height : '.$pre_screen['height_ft'].'  ft  '.$pre_screen['height_in'].'  in  ('.$pre_screen['height'].'cm)</td>';
                $html .='<td style="width: 20%">Weight : '.$pre_screen['weight'].' lbs  ('.round($pre_screen['weight']*0.45).'kg)</td>';
                $html .='</tr>';
                $html .='</table>';
                $html .='<div style="margin-top: 10px">';
                if ($pre_screen['marital_status']==1){
                    $html .='Are you married or living with a long term relationship partner : Yes';
                }else{
                    $html .='Are you married or living with a long term relationship partner : No';
                }
                $html .='</div>';
                $html .='</div>';
            }else{
//                $html .='<div>';
                $html .='<h3>Personal Information</h3>';
                $html .='<table>';
                $html .='<tr>';
                $html .='<td style="width: 20%">First name (名字): '.$pre_screen['first_name'].'</td>';
//            $html .='<td style="width: 20%">Last name : '.$pre_screen['last_name'].'</td>';
                $html .='<td style="width: 20%">Date of birth (生日): '.date("m-d-Y",$pre_screen['birthday_time']).'</td>';
                $html .='</tr>';
                $html .='<tr>';
                $html .='<td style="width: 20%">City (所在城市): '.$pre_screen['city'].'</td>';
                $html .='<td style="width: 20%">State (所在州): '.$pre_screen['state'].'</td>';
                $html .='<td style="width: 20%">Age (年龄): '.$pre_screen['age'].'</td>';
                $html .='</tr>';
                $html .='<tr>';
                $html .='<td style="width: 20%">Height (身高): '.$pre_screen['height_ft'].'  ft  '.$pre_screen['height_in'].'  in ('.$pre_screen['height'].'cm)</td>';
                $html .='<td style="width: 20%">Weight (体重): '.$pre_screen['weight'].' lbs ('.round($pre_screen['weight']*0.45).'kg)</td>';
                $html .='</tr>';
                $html .='</table>';
                $html .='<div style="margin-top: 10px">';
                if ($pre_screen['marital_status']==1){
                    $html .='Are you married or living with a long term relationship partner (是否已婚或有固定伴侣): Yes';
                }else{
                    $html .='Are you married or living with a long term relationship partner (是否已婚或有固定伴侣): No';
                }
                $html .='</div>';
                $html .='</div>';
            }

        }

        if ($personal_info){
            if ($type==1){
                $html .='<div style="margin-top: 20px">';
//            $html .='<h3>Personal Information</h3>';
                $html .='<table>';
                $html .='<tr>';
                $html .='<td style="width: 20%">Highest education : '.$personal_info['highest_education'].'</td>';
                $html .='<td style="width: 20%">Religion : '.$personal_info['religion'].'</td>';
                $html .='</tr>';
                $html .='<tr>';
                $html .='<td style="width: 20%">Ethnicity : '.$personal_info['race'].'</td>';
                $html .='<td style="width: 20%">Occupation : '.$personal_info['occupation'].'</td>';
                $html .='</tr>';
                $html .='</table>';
                $html .='</div>';
            }else{
                $html .='<div style="margin-top: 20px">';
//            $html .='<h3>Personal Information</h3>';
                $html .='<table>';
                $html .='<tr>';
                $html .='<td style="width: 20%">Highest education (教育水平): '.$personal_info['highest_education'].'</td>';
                $html .='<td style="width: 20%">Religion (宗教信仰): '.$personal_info['religion'].'</td>';
                $html .='</tr>';
                $html .='<tr>';
                $html .='<td style="width: 20%">Ethnicity (族裔): '.$personal_info['race'].'</td>';
                $html .='<td style="width: 20%">Occupation (职业): '.$personal_info['occupation'].'</td>';
                $html .='</tr>';
                $html .='</table>';
                $html .='</div>';
            }

        }



        if ($obstertric_history){
            if ($type==1){
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>Delivery History</h3>';
                $html .='<table border="1" cellspacing="0" class="ws_table">';
                $html .='<thead>';
                $html .='<tr>';
                $html .='<td style="width: 20%;" align="center">Date of delivery</td>';
                $html .='<td style="width: 20%" align="center">Gestational week at birth</td>';
                $html .='<td style="width: 20%" align="center">Type of delivery</td>';
                $html .='<td style="width: 20%" align="center">Is this a surrogacy pregnancy</td>';
                $html .='</tr>';
                $html .='</thead>';
                if ($surrogate_baby){
                    foreach ($surrogate_baby as $v){
                        $html .='<tr>';
                        $html .='<td align="center">'.date("m-d-Y",$v['birthday_time']).'</td>';
                        $html .='<td align="center">'.$v['fetal_age'].'</td>';
                        if ($v['is_surrogacy_pregnancy']==1){
                            $html .='<td align="center">vaginal</td>';
                        }else{
                            $html .='<td align="center">c-section</td>';
                        }

                        if ($v['is_surrogacy_pregnancy']==1){
                            $html .='<td align="center">Yes</td>';
                        }else{
                            $html .='<td align="center">No</td>';
                        }
                        $html .='</tr>';
                    }
                }

                $html .='</table>';


                if ($obstertric_history['is_artificial_abortion']==1){
                    $html .='<div style="margin-top: 10px">';
                    $html .='Is there a history of abortions : Yes';
                    $html .='</div>';
                    $html .='<div style="margin-top: 10px">';
                    $html .='If so, when : ' .$obstertric_history['artificial_abortion_content'];
                    $html .='</div>';
                }else{
                    $html .='<div style="margin-top: 10px">';
                    $html .='Is there a history of abortions : No';
                    $html .='</div>';
                }


                if ($obstertric_history['is_spontaneous_abortion']==1){
                    $html .='<div style="margin-top: 15px">';
                    $html .='Whether there is a history of miscarriages : Yes';
                    $html .='</div>';
                    $html .='<div style="margin-top: 10px">';
                    $html .='If so, when : ' .$obstertric_history['spontaneous_abortion_content'];
                    $html .='</div>';
                }else{
                    $html .='<div style="margin-top: 15px">';
                    $html .='Whether there is a history of miscarriages : No';
                    $html .='</div>';
                }
            }else{
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>Delivery History</h3>';
                $html .='<table border="1" cellspacing="0" class="ws_table">';
                $html .='<thead>';
                $html .='<tr>';
                $html .='<td style="width: 20%;" align="center">Date of delivery (生产日期)</td>';
                $html .='<td style="width: 20%" align="center">Gestational week at birth (孕周)</td>';
                $html .='<td style="width: 20%" align="center">Type of delivery (生产方式)</td>';
                $html .='<td style="width: 20%" align="center">Is this a surrogacy pregnancy(是否代孕)</td>';
                $html .='</tr>';
                $html .='</thead>';
                if ($surrogate_baby){
                    foreach ($surrogate_baby as $v){
                        $html .='<tr>';
                        $html .='<td align="center">'.date("m-d-Y",$v['birthday_time']).'</td>';
                        $html .='<td align="center">'.$v['fetal_age'].'</td>';
                        if ($v['is_surrogacy_pregnancy']==1){
                            $html .='<td align="center">vaginal (顺)</td>';
                        }else{
                            $html .='<td align="center">c-section (剖)</td>';
                        }

                        if ($v['is_surrogacy_pregnancy']==1){
                            $html .='<td align="center">Yes (是)</td>';
                        }else{
                            $html .='<td align="center">No (否)</td>';
                        }
                        $html .='</tr>';
                    }
                }

                $html .='</table>';


                if ($obstertric_history['is_artificial_abortion']==1){
                    $html .='<div style="margin-top: 10px">';
                    $html .='Is there a history of abortions (是否有堕胎) : Yes';
                    $html .='</div>';
                    $html .='<div style="margin-top: 10px">';
                    $html .='If so, when : ' .$obstertric_history['artificial_abortion_content'];
                    $html .='</div>';
                }else{
                    $html .='<div style="margin-top: 10px">';
                    $html .='Is there a history of abortions (是否有堕胎) : No';
                    $html .='</div>';
                }


                if ($obstertric_history['is_spontaneous_abortion']==1){
                    $html .='<div style="margin-top: 15px">';
                    $html .='Whether there is a history of miscarriages (是否有流产): Yes';
                    $html .='</div>';
                    $html .='<div style="margin-top: 10px">';
                    $html .='If so, when : ' .$obstertric_history['spontaneous_abortion_content'];
                    $html .='</div>';
                }else{
                    $html .='<div style="margin-top: 15px">';
                    $html .='Whether there is a history of miscarriages (是否有流产): No';
                    $html .='</div>';
                }
            }

        }





        if ($medical_information){
            if ($type==1){
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>Medical Information</h3>';

                if ($medical_information['is_smoke']== 1){
                    $html .='<div style="margin-top: 10px">Do you smoke : Yes</div>';
                    $html .='<div style="margin-top: 10px">How often and how much : '.$medical_information['smoke_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Do you smoke : No</div>';
                }

                if ($medical_information['is_alcoholic_beverages']== 1){
                    $html .='<div style="margin-top: 15px">Do you drink alcoholic beverages : Yes</div>';
                    $html .='<div style="margin-top: 10px">How often and how much: '.$medical_information['alcoholic_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Do you drink alcoholic beverages : No</div>';
                }

                if ($medical_information['is_take_any_medicine']== 1){
                    $html .='<div style="margin-top: 15px">Are you currently taking any medications : Yes</div>';
                    $html .='<div style="margin-top: 10px">Please describe in detail : '.$medical_information['any_medicine_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Are you currently taking any medications : No</div>';
                }

                if ($medical_information['is_allergic_medication']== 1){
                    $html .='<div style="margin-top: 15px">Are you allergic to any medication, food, animals, or anything else :</div>';
                    $html .='<div style="margin-top: 10px">Please describe in detail : '.$medical_information['allergic_medication_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Are you allergic to any medication, food, animals, or anything else : No</div>';
                }

                if ($medical_information['is_hospitalized']== 1){
                    $html .='<div style="margin-top: 15px">Have you ever been hospitalized, except childbirth : Yes</div>';
                    $html .='<div style="margin-top: 10px">Please describe in detail : '.$medical_information['hospitalized_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Have you ever been hospitalized, except childbirth : No</div>';
                }
                $html .='</div>';
            }else{
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>Medical Information</h3>';

                if ($medical_information['is_smoke']== 1){
                    $html .='<div style="margin-top: 10px">Do you smoke (是否吸烟): Yes</div>';
                    $html .='<div style="margin-top: 10px">How often and how much : '.$medical_information['smoke_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Do you smoke (是否吸烟): No</div>';
                }

                if ($medical_information['is_alcoholic_beverages']== 1){
                    $html .='<div style="margin-top: 15px">Do you drink alcoholic beverages (是否喝酒): Yes</div>';
                    $html .='<div style="margin-top: 10px">How often and how much: '.$medical_information['alcoholic_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Do you drink alcoholic beverages (是否喝酒): No</div>';
                }

                if ($medical_information['is_take_any_medicine']== 1){
                    $html .='<div style="margin-top: 15px">Are you currently taking any medications (目前是否有服用药物): Yes</div>';
                    $html .='<div style="margin-top: 10px">Please describe in detail : '.$medical_information['any_medicine_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Are you currently taking any medications (目前是否有服用药物): No</div>';
                }

                if ($medical_information['is_allergic_medication']== 1){
                    $html .='<div style="margin-top: 15px">Are you allergic to any medication, food, animals, or anything else (有无过敏):</div>';
                    $html .='<div style="margin-top: 10px">Please describe in detail : '.$medical_information['allergic_medication_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Are you allergic to any medication, food, animals, or anything else (有无过敏): No</div>';
                }

                if ($medical_information['is_hospitalized']== 1){
                    $html .='<div style="margin-top: 15px">Have you ever been hospitalized, except childbirth (是否住过院): Yes</div>';
                    $html .='<div style="margin-top: 10px">Please describe in detail : '.$medical_information['hospitalized_content'].'</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Have you ever been hospitalized, except childbirth (是否住过院): No</div>';
                }
                $html .='</div>';
            }

        }

        if ($about_surrogacy){
            if ($type==1){
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>About Surrogacy</h3>';

                if ($about_surrogacy['is_surrogate']== 1){
                    $html .='<div style="margin-top: 10px">Have you ever been a surrogate : Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Have you ever been a surrogate : No</div>';
                }

                $html .='<div style="margin-top: 10px">How soon would you like to begin your surrogacy journey : '.$about_surrogacy['begin_surrogate'].'</div>';
                $html .='<div style="margin-top: 10px">How many embryos are you willing to transfer at one time(1-2) : '.$about_surrogacy['embryo_transfer_number'].'</div>';

                if ($about_surrogacy['is_conceive_twins']== 1){
                    $html .='<div style="margin-top: 10px">Are you willing to carry twins : Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Are you willing to carry twins : No</div>';
                }

                if ($about_surrogacy['is_fetal_reduction']== 1){
                    $html .='<div style="margin-top: 10px">If you become pregnant with three or four fetuses and the parents choose to reduce to two or singleton,would you be willing to do selective reduction : Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">If you become pregnant with three or four fetuses and the parents choose to reduce to two or singleton,would you be willing to do selective reduction : No</div>';
                }

                if ($about_surrogacy['is_induced_abortion']== 1){
                    $html .='<div style="margin-top: 10px">If the fetus was diagnosed with medical conditions and abortion was suggested by the physician, upon the consents from intended parents, would you able to follow the doctor is order : Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">If the fetus was diagnosed with medical conditions and abortion was suggested by the physician, upon the consents from intended parents, would you able to follow the doctor is order : No</div>';
                }

                if ($about_surrogacy['is_cvs']== 1){
                    $html .='<div style="margin-top: 10px">If the parents want to have an amniocentesis done, would you be willing to : Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">If the parents want to have an amniocentesis done, would you be willing to : No</div>';
                }


                $html .='<div style="margin-top: 10px">During pregnancy, what kind of relationship do you want to maintain with your intended parents : '.$about_surrogacy['rwp_content'].'</div>';
                $html .='<div style="margin-top: 10px">After delivery, what kind of relationship do you want to maintain with your intended parents : '.$about_surrogacy['rwp_content_two'].'</div>';

                if ($about_surrogacy['is_sexual_life']== 1){
                    $html .='<div style="margin-top: 10px">Both you and your partner know that unless you have a tubal ligation or your partner has a vasectomy. you must agree that there is no sex life between receiving hormone therapy and embryo transfer? This does not mean that there is no sexual behavior during the whole surrogacy process. During the whole surrogacy process, you and your spouse is sexual life must be approved by your attending doctor : Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Both you and your partner know that unless you have a tubal ligation or your partner has a vasectomy. you must agree that there is no sex life between receiving hormone therapy and embryo transfer? This does not mean that there is no sexual behavior during the whole surrogacy process. During the whole surrogacy process, you and your spouse is sexual life must be approved by your attending doctor : No</div>';
                }


                $html .='<div style="margin-top: 10px">Does your family support you as a surrogate mother? Please provide details so that the intended parents know whether you have a family full of support and love : '.$about_surrogacy['family_content'].'</div>';
                $html .='</div>';
            }else{
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>About Surrogacy</h3>';

                if ($about_surrogacy['is_surrogate']== 1){
                    $html .='<div style="margin-top: 10px">Have you ever been a surrogate (是否有代孕经验): Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Have you ever been a surrogate (是否有代孕经验): No</div>';
                }

                $html .='<div style="margin-top: 10px">How soon would you like to begin your surrogacy journey (什么时候能开始): '.$about_surrogacy['begin_surrogate'].'</div>';
                $html .='<div style="margin-top: 10px">How many embryos are you willing to transfer at one time(1-2) (一次愿意移植几个胚胎): '.$about_surrogacy['embryo_transfer_number'].'</div>';

                if ($about_surrogacy['is_conceive_twins']== 1){
                    $html .='<div style="margin-top: 10px">Are you willing to carry twins (是否愿意怀双胎): Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Are you willing to carry twins (是否愿意怀双胎): No</div>';
                }

                if ($about_surrogacy['is_fetal_reduction']== 1){
                    $html .='<div style="margin-top: 10px">If you become pregnant with three or four fetuses and the parents choose to reduce to two or singleton,would you be willing to do selective reduction (是否愿意减胎): Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">If you become pregnant with three or four fetuses and the parents choose to reduce to two or singleton,would you be willing to do selective reduction (是否愿意减胎): No</div>';
                }

                if ($about_surrogacy['is_induced_abortion']== 1){
                    $html .='<div style="margin-top: 10px">If the fetus was diagnosed with medical conditions and abortion was suggested by the physician, upon the consents from intended parents, would you able to follow the doctor is order (是否愿意医疗原因堕胎): Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">If the fetus was diagnosed with medical conditions and abortion was suggested by the physician, upon the consents from intended parents, would you able to follow the doctor is order (是否愿意医疗原因堕胎): No</div>';
                }

                if ($about_surrogacy['is_cvs']== 1){
                    $html .='<div style="margin-top: 10px">If the parents want to have an amniocentesis done, would you be willing to (能不能接受做羊水穿刺): Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">If the parents want to have an amniocentesis done, would you be willing to (能不能接受做羊水穿刺): No</div>';
                }


                $html .='<div style="margin-top: 10px">During pregnancy, what kind of relationship do you want to maintain with your intended parents (孕期想跟准父母保持什么样的关系): '.$about_surrogacy['rwp_content'].'</div>';
                $html .='<div style="margin-top: 10px">After delivery, what kind of relationship do you want to maintain with your intended parents (产后想跟准父母保持什么样的关系): '.$about_surrogacy['rwp_content_two'].'</div>';

                if ($about_surrogacy['is_sexual_life']== 1){
                    $html .='<div style="margin-top: 10px">Both you and your partner know that unless you have a tubal ligation or your partner has a vasectomy. you must agree that there is no sex life between receiving hormone therapy and embryo transfer? This does not mean that there is no sexual behavior during the whole surrogacy process. During the whole surrogacy process, you and your spouse is sexual life must be approved by your attending doctor (孕期能否根据医生要求禁欲): Yes</div>';
                }else{
                    $html .='<div style="margin-top: 10px">Both you and your partner know that unless you have a tubal ligation or your partner has a vasectomy. you must agree that there is no sex life between receiving hormone therapy and embryo transfer? This does not mean that there is no sexual behavior during the whole surrogacy process. During the whole surrogacy process, you and your spouse is sexual life must be approved by your attending doctor (孕期能否根据医生要求禁欲): No</div>';
                }


                $html .='<div style="margin-top: 10px">Does your family support you as a surrogate mother? Please provide details so that the intended parents know whether you have a family full of support and love (家人支持您做代母吗？): '.$about_surrogacy['family_content'].'</div>';
                $html .='</div>';
            }

        }

        if ($other_information){
            if ($type==1){
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>Other Information</h3>';
                $html .='<div style="margin-top: 10px">What is your hobby : '.$other_information['hobby'].'</div>';
                $html .='<div style="margin-top: 10px">What is your life goal : '.$other_information['life_geyan'].'</div>';
                $html .='<div style="margin-top: 10px">How would you explain to your child that you became a surrogate mother : '.$other_information['ecybs'].'</div>';
                $html .='<div style="margin-top: 10px">Who else is/are living in your house with you : '.$other_information['living'].'</div>';
                $html .='<div style="margin-top: 10px">Do you have anything you would like to share to your future intended parents? : '.$other_information['share_parents'].'</div>';
                $html .='</div>';
            }else{
                $html .='<div style="margin-top: 20px">';
                $html .='<h3>Other Information</h3>';
                $html .='<div style="margin-top: 10px">What is your hobby (您的兴趣爱好是什么): '.$other_information['hobby'].'</div>';
                $html .='<div style="margin-top: 10px">What is your life goal (您有什么人生目标): '.$other_information['life_geyan'].'</div>';
                $html .='<div style="margin-top: 10px">How would you explain to your child that you became a surrogate mother (您要如何跟孩子解释代孕？): '.$other_information['ecybs'].'</div>';
                $html .='<div style="margin-top: 10px">Who else is/are living in your house with you (家里都有什么人住在一起？): '.$other_information['living'].'</div>';
                $html .='<div style="margin-top: 10px">Do you have anything you would like to share to your future intended parents? (你有什么想和准父母分享的吗?): '.$other_information['share_parents'].'</div>';
                $html .='</div>';
            }



        }




        $html .='</div>';
        $html .='</body>';
        $html .='</html>';



        return $html;
    }

    function random_code($length = 8,$chars = null){
        if(empty($chars)){
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }
        $count = strlen($chars) - 1;
        $code = '';
        while( strlen($code) < $length){
            $code .= substr($chars,rand(0,$count),1);
        }
        return $code;
    }

    //生成代母word
    public function surrogacy_word(){
        $params = input();
//        dump($params);die;
        $uid = $params['ids'];
        //模板的路径，word的版本最好是docx，要不然可能会读取不了，根据自己的模板位置调整
        $path = 'uploads/word/surrogacy.docx';
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

        $surrogate_photo = \app\admin\model\SurrogatePhoto::where('uid',$uid)->value('images');

        if($surrogate_photo){
            $surrogate_photo = explode(',',$surrogate_photo);
            $img_count = count($surrogate_photo);
            $templateProcessor->cloneBlock('block_name', $img_count, true, true);
            for($i=0;$i<$img_count;$i++){
                $img = $surrogate_photo[$i];
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

        $pre_screen = \app\admin\model\PreScreen::where('uid',$uid)->find();
        $pre_screen = $pre_screen?$pre_screen->toArray():$pre_screen;
        if ($pre_screen){
            $templateProcessor->cloneBlock('pre_screen', 1, true, false);

            $height = round(($pre_screen['height_ft']*12*2.36) + ($pre_screen['height_in']*2.36)).'cm';
            $kg = round($pre_screen['weight']*0.45).'kg';
            $templateProcessor->setValue('first_name',$pre_screen['first_name']);
            $templateProcessor->setValue('birthday_time',date('m-d-Y',$pre_screen['birthday_time']));
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

        $personal_info = \app\admin\model\PersonalInfo::where('uid',$uid)->find();
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
                    $templateProcessor->setValue("ob_date#".($i+1),date('m-d-Y',$surrogate_baby[$i]['birthday_time']));
                    $templateProcessor->setValue("fetal_age#".($i+1),$surrogate_baby[$i]['fetal_age']);
                    $templateProcessor->setValue("pregnancy_type#".($i+1),$surrogate_baby[$i]['pregnancy_type']?'c-setion':'vaginal');
                    $templateProcessor->setValue("is_surrogacy_pregnancy#".($i+1),$surrogate_baby[$i]['is_surrogacy_pregnancy']==1?'Yes':'No');
                }
            }else{
                $templateProcessor->cloneBlock('baby', 0, true, false);
            }
            $templateProcessor->setValue("is_artificial_abortion",$obstertric_history['is_artificial_abortion']==1?'Yes':'NO');
            $templateProcessor->cloneBlock('b_artificial', 1, true, false);
            if ($obstertric_history['is_artificial_abortion']=='1'){
                $templateProcessor->setValue("artificial_abortion_content",$obstertric_history['artificial_abortion_content']);
            }else{
                $templateProcessor->setValue("artificial_abortion_content",'');
                $templateProcessor->cloneBlock('b_artificial', 0, true, false);

            }

            $templateProcessor->setValue("is_spontaneous_abortion",$obstertric_history['is_spontaneous_abortion']==1?'Yes':'NO');

            $templateProcessor->cloneBlock('b_spontaneous', 1, true, false);
            if ($obstertric_history['is_spontaneous_abortion']=='1'){
                $templateProcessor->setValue("spontaneous_abortion_content",$obstertric_history['spontaneous_abortion_content']);
            }else{
                $templateProcessor->setValue("spontaneous_abortion_content",'');
                $templateProcessor->cloneBlock('b_spontaneous', 0, true, false);
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
            $templateProcessor->setValue("share_parents",$other_information['share_parents']);
        }else{
            $templateProcessor->cloneBlock('other', 0, true, false);
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

    public function notes($ids=null){
        $surrogacy = \app\admin\model\User::get(['id' => $ids]);

        $this->model = new \app\admin\model\NotesSurrogacy;
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = NotesSurrogacy::where('form_id',$ids)
                ->with('admin')
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->view->assign("surrogacy", $surrogacy);
        $this->view->assign("ids", $ids);
        return $this->view->fetch();
    }
    public function notes_add(){

        $params = input();
        $status = $params['status'];
        $params['admin_id'] = $this->auth->id;
        $model = NotesSurrogacy::where('form_id',$params['form_id'])->order('createtime','desc')->find();
        if ($model){
            $createtime = $model['createtime'];
            if (is_string($createtime)){
                $b = explode(' ',$createtime);
                $c = explode('-',$b[0]);
                $createtime = $c[2].'-'.$c[0].'-'.$c[1].' '.$b[1];
                $createtime = strtotime($createtime);
            }
            $time_duan = time() - $createtime;

//            dump($createtime);
//            dump($time_duan);die;
            if ($time_duan < 300 && $model['content'] == $params['content']&&$model['status']==$status) {
                return json(['code' => 3, 'msg' => "Do not submit the same follow-up record within five minutes"]);
            }
        }

        $res = NotesSurrogacy::create($params);
        if ($res){
            \app\admin\model\User::where('id',$params['form_id'])->setInc('follow_ups',1);
            \app\admin\model\User::where('id',$params['form_id'])->update(['examine_status'=>$status]);
            return json(['code'=>1,'msg'=>"Success",'res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>"Fail"]);
        }


    }

    public function progress(){
        $data = [
            ['id'=>'prescreen', 'name'=>'Pre_screening'],
            ['id'=>'personal', 'name'=>'Personal Information'],
            ['id'=>'ob', 'name'=>'Obstetric History'],
            ['id'=>'medical', 'name'=>'Medical Information'],
            ['id'=>'about', 'name'=>'About Surrogacy'],
            ['id'=>'other', 'name'=>'Other Information'],
            ['id'=>'photo', 'name'=>'Photos'],
            ['id'=>'health', 'name'=>'Health Record Release'],
            ['id'=>'background', 'name'=>'Background'],
            ['id'=>'sbp', 'name'=>'SBP'],
        ];
        $result = ['rows' => $data];
        return json($result);
    }
}
