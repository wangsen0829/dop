<?php

namespace app\admin\controller\lead;

use app\admin\model\Admin;
use app\admin\model\AuthGroupAccess;
use app\admin\model\DonorLead;
use app\admin\model\DonorPreScreen;
use app\admin\model\NewLead;
use app\admin\model\Notes;
use app\admin\model\PreScreen;
use app\admin\model\SpermDonationLead;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\library\Auth;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Hook;
use think\Model;
use think\response\Json;
use think\Config;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Index extends Backend
{

    /**
     * NewLead模型对象
     * @var \app\admin\model\NewLead
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'id,email,mobile,first_name,last_name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\NewLead;


        $this->view->assign("serviceList", Config::get('site.service'));
        $this->view->assign("statusList", Config::get('site.status'));
        $this->view->assign("leadSourceList", Config::get('site.lead_source'));
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("isRepeatList", $this->model->getIsRepeatList());

        //筛选角色是员工
        $where1['id'] = array('not in','1');
        $where1['switch'] = 1;
        $user = \app\admin\model\Admin::order('id','asc')->where($where1)->select();
        $this->view->assign("user", $user);

        //referral
        $referral = \app\admin\model\Admin::order('id','asc')->where($where1)->column('id,username');
        $this->view->assign("referral", $referral);
//        dump($referral);die;

        $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
        $this->view->assign("role_id", $role_id);
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        $where1=[];
        $filter = json_decode($this->request->get('filter'), true);
        $op = json_decode($this->request->get("op"),true);
        //筛选status=5的数据 需要去联系
        if (isset($filter['status'])&&$filter['status']==5){
            unset($filter['status']);
            unset($op['status']);
            $time= strtotime(date('Y-m-d 23:59:59', time()));
            $form_ids = Notes::where('contact_status',1)
                ->group('form_id')
                ->order('createtime','desc')->column('form_id');
            $form_ids = implode(',',$form_ids);
            $filter['id'] = $form_ids;
            $op['id'] = "in";
            $this->request->get(["filter"=>json_encode($filter),'op'=>json_encode($op)]);
        }else if(isset($filter['updatetime'])){
            //筛选时间段内跟进的数据
            $time = explode(' - ',$filter['updatetime']);
            $start_time = strtotime($time[0]);
            $end_time = strtotime($time[1]);
            $where_notes['createtime'] = ['between time', [$start_time, $end_time]];
            $form_ids = Notes::where($where_notes)
                ->group('form_id')
                ->order('createtime','desc')->column('form_id');
            $form_ids = implode(',',$form_ids);
            unset($filter['updatetime']);
            unset($op['updatetime']);
            $filter['id'] = $form_ids;
            $op['id'] = "in";
            $this->request->get(["filter"=>json_encode($filter),'op'=>json_encode($op)]);
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        //员工分配线索
        $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
        if ($role_id==7){
            $where1['admin_id'] = $this->auth->id;
        }
        $list = $this->model
            ->with('admin')
            ->where($where)
            ->where($where1)
//            ->order('createtime','desc')
            ->order('id','desc')
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        $surrogacy =  $this->request->post('surrogacy/a');
        $donorLead = $this->request->post('donor/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        //判断线索是否存在（邮箱）
        $new_lead_model = NewLead::where('email',$params['email'])->find();

        //添加分配者
        if ($new_lead_model){
            if ($new_lead_model['admin_id']){
                $admin_id = $new_lead_model['admin_id'];
            }else{
                $admin_id = owner();
            }
        }else{
            $admin_id = owner();
        }

        $service = $params['service'];

        //实例化NewLead模型
        $Newlead = new NewLead();
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->middle_name = $params['middle_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['mobile'];
        //状态
        $Newlead->service = $service;
        $Newlead->status = $params['status'];
        $Newlead->type = 1; //后台添加
        $Newlead->admin_id = $admin_id;
        $Newlead->is_repeat = $new_lead_model?2:1;
        //API information
        $Newlead->lead_source = $params['lead_source'];
        $Newlead->media = $params['media'];
        $Newlead->account = $params['account'];
        $Newlead->campaign = $params['campaign'];
        $Newlead->adgroup = $params['adgroup'];
        $Newlead->adname = $params['adname'];
        $Newlead->adname = $params['adname'];
        $Newlead->referral_id = $params['referral']?$params['referral']:null;
        if ($params['referral']){
            $referral = Admin::where('id',$params['referral'])->value('username');
            $Newlead->referral = $referral;
            $Newlead->admin_id = $params['referral'];
        }

        $Newlead->message = $params['message'];
        $bmi = '';
        if ($service==1){
            //实例化SurrogacyLead模型 关联表surrogacy_lead
            if ($surrogacy['height_ft']&&$surrogacy['height_in']){
                $ft = $surrogacy['height_ft'];
                $in = $surrogacy['height_in'];
                $lbs = $surrogacy['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $bmi = round($bmi);
            }
            $surrogacy['bmi'] = $bmi;
            $Newlead->surrogacy = $surrogacy; //重点
            $res = $Newlead->together('surrogacy')->save();
        }elseif ($service==2){
            //实例化DonorLead模型 关联表donor_lead
            if ($donorLead['height_ft']&&$donorLead['height_in']){
                $ft = $donorLead['height_ft'];
                $in = $donorLead['height_in'];
                $lbs = $donorLead['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $bmi = round($bmi);
            }
            $donorLead['bmi'] = $bmi;
            $Newlead->donor = $donorLead; //重点
            $res = $Newlead->together('donor')->save();
        }else{
            $res = $Newlead->save();
        }
        if ($res === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->with('surrogacy,donor')->find($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        $surrogacy = $this->request->post('surrogacy/a');
        $donorLead = $this->request->post('donor/a');

        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $service = $params['service'];

        //实例化NewLead模型
        $Newlead = NewLead::get($ids);
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->middle_name = $params['middle_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['mobile'];
        //状态
        $Newlead->service = $service;
        if (isset($params['status'])){
            $Newlead->status = $params['status'];
        }
        //API information
        $Newlead->lead_source = $params['lead_source'];
        $Newlead->media = $params['media'];
        $Newlead->account = $params['account'];
        $Newlead->campaign = $params['campaign'];
        $Newlead->adgroup = $params['adgroup'];
        $Newlead->adname = $params['adname'];
        $Newlead->adname = $params['adname'];
        $Newlead->referral = $params['referral'];

        if ($service==1){
            //实例化SurrogacyLead模型 关联表surrogacy_lead
            if ($surrogacy['height_ft']&&$surrogacy['height_in']){
                $ft = $surrogacy['height_ft'];
                $in = $surrogacy['height_in'];
                $lbs = $surrogacy['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $bmi = round($bmi);
            }
            $surrogacy['bmi'] = $bmi;
            $Newlead->surrogacy = $surrogacy; //重点
            $res = $Newlead->together('surrogacy')->save();
        }elseif ($service==2){
            //实例化DonorLead模型 关联表donor_lead
            if ($donorLead['height_ft']&&$donorLead['height_in']){
                $ft = $donorLead['height_ft'];
                $in = $donorLead['height_in'];
                $lbs = $donorLead['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $bmi = round($bmi);
            }
            $donorLead['bmi'] = $bmi;
            $Newlead->donor = $donorLead; //重点
            $res = $Newlead->together('donor')->save();
        }else{
            $res = $Newlead->save();
        }
        if (false === $res) {
            $this->error(__('No rows were updated'));
        }
        Hook::listen('precreen_edit_success',$ids);
        $this->success();
    }


    //详情
    public function detail($ids)
    {
        $row = $this->model->with('surrogacy,donor')->find($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $this->success("Ajax请求成功", null, ['id' => $ids]);
        }
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

    //返回线索来源数组
    public function lead_source(){
        $lead_source = Config::get('site.lead_source');
        $arr = [];
        foreach ($lead_source as $k=>$v){
            $arr[] = [
              'id'=>$k,
              'name'=>$v,
            ];
        }
        $result = ['rows' => $arr];
        return json($result);
    }

    public function notes($ids=null){
        $form = \app\admin\model\NewLead::with('surrogacy')->find($ids);

        $this->model = new \app\admin\model\Notes;
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = Notes::where('form_id',$ids)
                ->with('admin')
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->view->assign("form", $form);
        $this->view->assign("ids", $ids);
        return $this->view->fetch();
    }

    public function notes_add(){

        $params = input();
        $params['admin_id'] = $this->auth->id;
        if ($params['nexttime']){
            $params['nexttime'] = strtotime($params['nexttime']);
        }
        $status = $params['caozuo'];

        if ($status =='3'){
            $params['contact_status']= '1';
        }else{
            $params['nexttime'] = null;
            $params['contact_status']= '2';
            Notes::where('form_id',$params['form_id'])->update(['contact_status'=>'2']);
        }

        $model = Notes::where('form_id',$params['form_id'])->order('createtime','desc')->find();

        if ($model){
            $createtime = $model['createtime'];
            if (is_string($createtime)){
                $b = explode(' ',$createtime);
                $c = explode('-',$b[0]);
                $createtime = $c[2].'-'.$c[0].'-'.$c[1].' '.$b[1];
                $createtime = strtotime($createtime);
            }
            $time_duan = time() - $createtime;
            if ($time_duan < 300 && $model['content'] == $params['content']&&$model['caozuo']==$status) {
                return json(['code' => 3, 'msg' => "Do not submit the same follow-up record within five minutes"]);
            }
        }
        $res = Notes::create($params);

        if ($res){
            \app\admin\model\NewLead::where('id',$params['form_id'])->setInc('follow_ups',1);
            \app\admin\model\NewLead::where('id',$params['form_id'])->update(['status'=>$status]);
            return json(['code'=>1,'msg'=>"Success"]);
        }else{
            return json(['code'=>2,'msg'=>"Fail"]);
        }

    }

    public function notes_del(){
        $params = input();
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $params['ids'];
        $form_id = $params['form_id'];
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $res = Notes::where('id',$ids)->delete();
        if ($res){
            \app\admin\model\PreScreenForm::where('id',$form_id)->setDec('follow_ups');
            $this->success();
        }

    }

    //线索审核
    public function examine($ids){

        $data = $this->model->find($ids);

        //如果状态为1，禁止pass
        $model_status = $data['status'];
        $where = [
            'email'=>$data['email'],
            'service'=>$data['service'],
        ];
        $user = User::where($where)->find();

        if ($model_status==1&&$user){
            return json(['code'=>5,'msg'=>'The clue has been passed']);
        }
        //添加操作记录
        $Notes = [
            'form_id'=>$ids,
            'contact_status'=>'2',
        ];
        if ($user){
            //修改状态为2 重复线索修改状态为dq
            $res = $this->model->where('id',$ids)->update(['status'=>2]);
            //添加操作记录
            $Notes['content'] ='System: Form duplicate submission' ;
            $Notes['caozuo'] = 2 ;
            Hook::listen('notes_add',$Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return json(['code'=>2,'msg'=>'This email has already been registered','data'=>$res]);
        }

        $a_id = $this->model->where($where)->order('createtime','asc')->value('id');
        if ($ids!=$a_id){
            return json(['code'=>4,'msg'=>'The clue is duplicated, please pass the first clue on the timeline']);
        }
        //添加用户账号
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $password = '123456';
        $email = $data['email'];
        $mobile = $data['mobile'];
        $extend = [
            'type'=>2,
            'lead_id'=>$ids,
            'lead_source'=>$data['lead_source'],
            'media'=>$data['media'],
            'account'=>$data['account'],
            'campaign'=>$data['campaign'],
            'adgroup'=>$data['adgroup'],
            'adname'=>$data['adname'],
            'referral'=>$data['referral'],
            'referral_id'=>$data['referral_id'],
            'admin_id'=>$data['admin_id'],
            'service'=>$data['service'],
        ];

        $auth = new Auth();
        $res = $auth->register($first_name,$last_name, $password, $email, $mobile,$extend);
        if (!$res) {
            $msg = $auth->getError();
            //添加操作记录
            $Notes['content'] ='System:' . $msg ;
            $Notes['caozuo'] = 1 ;
            Hook::listen('notes_add',$Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return json(['code'=>3,'msg'=>$msg,'data'=>$res]);
        }

        //修改预审核表状态 为审核通过
        $this->model->where('id',$ids)->update(['status'=>1]);
        //审核通过，修改note表状态都为2；
        Notes::where('form_id',$ids)->update(['contact_status'=>2]);
        $uid = User::where('lead_id',$ids)->value('id');
        if ($data['service']==1){
            $pre_screen = PreScreen::where('uid',$uid)->find();
            $red = [];
            if (!$pre_screen){
                $red['uid'] = $uid;
                $red['first_name'] = $data['first_name'];
                $red['middle_name'] = $data['middle_name'];
                $red['last_name'] = $data['last_name'];
                $red['email'] = $data['email'];
                $red['mobile'] = $data['mobile'];
                $red['birthday_time'] = $data['surrogacy']['birthday_time'];
                $red['address'] = $data['surrogacy']['address'];
                $red['city'] = $data['surrogacy']['city'];
                $red['state'] = $data['surrogacy']['state'];
                $red['postal_code'] = $data['surrogacy']['postal_code'];
                $red['age'] = $data['surrogacy']['age'];
                $red['height_ft'] = $data['surrogacy']['height_ft'];
                $red['height_in'] = $data['surrogacy']['height_in'];
                $red['bmi'] = $data['surrogacy']['bmi'];
                $red['weight'] = $data['surrogacy']['weight'];
                $red['marital_status'] = $data['surrogacy']['marital_status'];
                $red['is_us'] = $data['surrogacy']['is_us'];
                $red['product_number'] = $data['surrogacy']['product_number'];
                $red['caesarean_number'] = $data['surrogacy']['caesarean_number'];
                $red['miscarriage_number'] = $data['surrogacy']['miscarriage_number'];
                $red['abortion_number'] = $data['surrogacy']['abortion_number'];
                $red['is_abortion_reason'] = $data['surrogacy']['is_abortion_reason'];
                $red['abortion_reason'] = $data['surrogacy']['abortion_reason'];
                $red['is_complications_of_pregnancy'] = $data['surrogacy']['is_complications_of_pregnancy'];
                $red['complications_of_pregnancy'] = $data['surrogacy']['complications_of_pregnancy'];
                $red['contraceptive_measures'] = $data['surrogacy']['contraceptive_measures'];
                $red['about_content'] = $data['surrogacy']['about_content'];

                //判断表单完成进度
                if ($data['lead_source']=='vip.dopusa.com'&&$data['type']==2){
                    $red['status'] = 1;
                    $red['form_complate_number'] = 22;
                }else{
                    $red['form_complate_number'] = 4;
                }
                $res = Prescreen::create($red);
                if ($res){
                    //发送邮件 代母审核通过
                    $username = $first_name.$last_name;
                    $mail = $data['email'] ;
                    $pass ='123456';
                    $admin = $this->auth->username;
                    $bcc_email = $this->auth->email;
                    $type = 1 ;  //代母线索审核通过
                    $uid = $this->auth->id;
                    $ret = send_email($username,$mail,$pass,$admin,$bcc_email,$type,$uid);
                    if ($ret['code'] !=1){
                        //添加操作记录
                        $Notes['content'] ='System:Sending email failed' ;
                        $Notes['caozuo'] = 1 ;
                        Hook::listen('notes_add',$Notes);
                        $this->model->where('id',$ids)->setInc('follow_ups');
                        return  json(['code'=>1,'msg'=>'Sending email failed','data'=>$res]);
                    }

                    //添加操作记录
                    $Notes['content'] ='System:Email sent successfully and approved' ;
                    $Notes['caozuo'] = 1 ;
                    Hook::listen('notes_add',$Notes);
                    $this->model->where('id',$ids)->setInc('follow_ups');

                    return  json(['code'=>1,'msg'=>'Audit successful','data'=>$res]);
                }else{
                    //添加操作记录
                    $Notes['content'] ='System:Adding Prescreen table failed' ;
                    $Notes['caozuo'] = 1 ;
                    Hook::listen('notes_add',$Notes);
                    $this->model->where('id',$ids)->setInc('follow_ups');
                    return  json(['code'=>4,'msg'=>'Adding Prescreen table failed','data'=>$res]);
                }

            }else{
                //添加操作记录
                $Notes['content'] ='System:Prescreen table data already exists' ;
                $Notes['caozuo'] = 1 ;
                Hook::listen('notes_add',$Notes);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return json(['code'=>5,'msg'=>'Prescreen table data already exists','data'=>$res]);
            }
        }elseif ($data['service']==2){
            $donor_pre_screen = DonorPreScreen::where('uid',$uid)->find();
            $red = [];
            if (!$donor_pre_screen){
                $red['uid'] = $uid;
                $red['first_name'] = $data['first_name'];
                $red['last_name'] = $data['last_name'];
                $red['email'] = $data['email'];
                $red['mobile'] = $data['mobile'];
                $red['state'] = $data['donor']['state'];
                $red['age'] = $data['donor']['age'];
                $red['height_ft'] = $data['donor']['height_ft'];
                $red['height_in'] = $data['donor']['height_in'];
                $red['weight'] = $data['donor']['weight'];
                $red['bmi'] = $data['donor']['bmi'];
                $red['ethnicity'] = $data['donor']['ethnicity'];
                $red['blood_type'] = $data['donor']['blood_type'];
                $red['place_of_birth'] = $data['donor']['place_of_birth'];
                $red['highest_education'] = $data['donor']['highest_education'];
                $red['occupation'] = $data['donor']['occupation'];
                $red['is_donated'] = $data['donor']['is_donated'];
                $red['is_smoke'] = $data['donor']['is_smoke'];
                $red['is_drink'] = $data['donor']['is_drink'];
                $red['is_illicit_drugs'] = $data['donor']['is_illicit_drugs'];
                $red['is_any_medications'] = $data['donor']['is_any_medications'];
                $red['abortion_reason'] = $data['donor']['abortion_reason'];
                //判断表单完成进度
                if ($data['lead_source']=='vip.dopusa.com'&&$data['type']==2){
                    $red['status'] = 1;
                    $red['form_complate_number'] = 23;
                }else{
                    $red['form_complate_number'] = 4;
                }


                $res = DonorPreScreen::create($red);
                if ($res){
                    //发送邮件 捐卵审核通过
                    $username = $first_name.$last_name;
                    $mail = $data['email'] ;
                    $pass ='123456';
                    $admin = $this->auth->username;
                    $bcc_email = $this->auth->email;
                    $type = 4 ;  //代母线索审核通过
                    $uid = $this->auth->id;
                    $ret = send_email($username,$mail,$pass,$admin,$bcc_email,$type,$uid);
                    if ($ret['code'] !=1){
                        //添加操作记录
                        $Notes['content'] ='System:Sending email failed' ;
                        $Notes['caozuo'] = 1 ;
                        Hook::listen('notes_add',$Notes);
                        $this->model->where('id',$ids)->setInc('follow_ups');
                        return  json(['code'=>1,'msg'=>'Sending email failed','data'=>$res]);
                    }

                    //添加操作记录
                    $Notes['content'] ='System:Email sent successfully and approved' ;
                    $Notes['caozuo'] = 1 ;
                    Hook::listen('notes_add',$Notes);
                    $this->model->where('id',$ids)->setInc('follow_ups');

                    return  json(['code'=>1,'msg'=>'Audit successful','data'=>$res]);
                }else{
                    //添加操作记录
                    $Notes['content'] ='System:Adding Prescreen table failed' ;
                    $Notes['caozuo'] = 1 ;
                    Hook::listen('notes_add',$Notes);
                    $this->model->where('id',$ids)->setInc('follow_ups');
                    return  json(['code'=>4,'msg'=>'Adding Prescreen table failed','data'=>$res]);
                }

            }else{
                //添加操作记录
                $Notes['content'] ='System:Prescreen table data already exists' ;
                $Notes['caozuo'] = 1 ;
                Hook::listen('notes_add',$Notes);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return json(['code'=>5,'msg'=>'Prescreen table data already exists','data'=>$res]);
            }
        }





    }

    //线索拒绝
    public function refuse(){

        $params = input();
        $ids = $params['ids'];
        $dq_content = $params['dq_content'];
        $data = $this->model->find($ids);
        if ($data['status']==2){
            return json(['code'=>5,'msg'=>'The clue has been DQed']);
        }

        //添加操作记录
        $Notes = ['form_id'=>$ids, 'contact_status'=>'2'];
        $where = [
            'email'=>$data['email'],
            'service'=>$data['service'],
        ];
        $user = User::where($where)->find();
        if ($user&&$user['lead_id']!=$ids){
            //修改状态为3 重复提交
            $res = $this->model->where('id',$ids)->update(['status'=>2]);
            //添加操作记录
            $Notes['content'] ='System: Form duplicate submission' ;
            $Notes['caozuo'] = 2;
            Notes::create($Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return json(['code'=>2,'msg'=>'This email has already been registered','data'=>$res]);
        }
        $res = $this->model->where('id',$ids)->update(['status'=>2,'dq_content'=>$dq_content]);

        if ($res){
            //发送邮件
            $admin = $this->auth->username;
            $bcc_email = $this->auth->email;
            $uid = $this->auth->id;
            $mail = $data['email'] ;
            if ($data['service']==1){
                $type =2 ; //代母线索审核拒绝
            }elseif ($data['service']==2){
                $type =5 ; //代母线索审核拒绝
            }

            $pass ='123456';
            $username = $data['first_name'].$data['last_name'];
            $ret = send_email($username,$mail,$pass,$admin,$bcc_email,$type,$uid);

            if ($ret['code'] ==1){
                //添加操作记录
                $Notes['content'] = $dq_content;
                $Notes['caozuo'] = 2 ;
                Hook::listen('notes_add',$Notes);
                Notes::where('form_id',$ids)->update(['contact_status'=>2]);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return  json(['code'=>1,'msg'=>'Rejected successfully, email sent successfully','data'=>$res]);
            }else{
                //添加操作记录
                $Notes['content'] = 'System:Rejected successfully, failed to send email';
                $Notes['caozuo'] = 2 ;
                Hook::listen('notes_add',$Notes);
                Notes::where('form_id',$ids)->update(['contact_status'=>2]);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return  json(['code'=>1,'msg'=>'Rejected successfully, failed to send email','data'=>$res]);
            }

        }else{
            $Notes['content'] = 'System:Rejection failed';
            $Notes['caozuo'] = 2 ;
            Hook::listen('notes_add',$Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return  json(['code'=>2,'msg'=>'Rejection failed']);
        }

    }

    //线索分配
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
            return json(['code'=>0,'msg'=>'Allocation failure']);
        }
    }
}
