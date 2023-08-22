<?php

namespace app\admin\controller\user;
use app\common\library\Email;
use app\common\controller\Backend;
use app\admin\model\User;
use app\admin\model\PreScreen;
use app\admin\model\Notes;
use app\common\library\Auth;
use app\admin\model\AuthGroupAccess;
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
 * 
 *
 * @icon fa fa-circle-o
 */

class PreScreenForm extends Backend
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    /**
     * PreScreenForm模型对象
     * @var \app\admin\model\PreScreenForm
     */

    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'id,email,mobile,first_name,last_name';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\PreScreenForm;
        $this->view->assign("maritalStatusList", $this->model->getMaritalStatusList());
        $this->view->assign("statusList", $this->model->getStatusList());

        //筛选角色是员工
//        $uid = AuthGroupAccess::where('group_id',7)->column('uid');
//        $user = \app\admin\model\Admin::order('id','asc')->where('id','in',$uid)->select();
        $where1['id'] = array('not in','1');
        $user = \app\admin\model\Admin::order('id','asc')->where($where1)->select();
        $this->view->assign("user", $user);

        $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
        $this->view->assign("role_id", $role_id);
    }


    public function status(){
        $a = $this->model->getStatusList() ;
        return json($a);
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
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        $where1=[];
        $filter = json_decode($this->request->get('filter'), true);
        $op = json_decode($this->request->get("op"),true);
        if (isset($filter['status'])&&$filter['status']==5){
            unset($filter['status']);
            unset($op['status']);
//            date_default_timezone_set('PRC');
            $time= strtotime(date('Y-m-d 23:59:59', time()));
            $form_ids = Notes::where('contact_status',1)->where('nexttime','<',$time)
                ->group('form_id')
                ->order('createtime','desc')->column('form_id');
//            dump($form_ids);die;
            $form_ids = implode(',',$form_ids);
            $filter['id'] = $form_ids;
            $op['id'] = "in";
            $this->request->get(["filter"=>json_encode($filter),'op'=>json_encode($op)]);
        }else if(isset($filter['updatetime'])){

//            date_default_timezone_set('PRC');
             $time = explode(' - ',$filter['updatetime']);
             $start_time = strtotime($time[0]);
             $end_time = strtotime($time[1]);
            $where_notes['createtime'] = ['between time', [$start_time, $end_time]];
            $form_ids = Notes::where($where_notes)
                ->group('form_id')
                ->order('createtime','desc')->column('form_id');

            $form_ids = implode(',',$form_ids);
//            dump($form_ids);die;
            unset($filter['updatetime']);
            unset($op['updatetime']);
            $filter['id'] = $form_ids;
            $op['id'] = "in";
            $this->request->get(["filter"=>json_encode($filter),'op'=>json_encode($op)]);
        }

         [$where, $sort, $order, $offset, $limit] = $this->buildparams();


            $role_id = AuthGroupAccess::where('uid',$this->auth->id)->value('group_id');
            if ($role_id==7){
                $where1['admin_id'] = $this->auth->id;
            }
            $list = $this->model
                ->with('admin')
                ->where($where)
                ->where($where1)
                ->order('createtime','desc')
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
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            if ($params['createtime']){
                $params['createtime']=strtotime($params['createtime']);
            }
            if ($params['height_ft']&&$params['height_in']){
                $ft = $params['height_ft'];
                $in = $params['height_in'];
                $lbs = $params['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $params['bmi'] = round($bmi);
            }
            $model = \app\admin\model\PreScreenForm::where('email',$params['email'])->find();
            if ($model){
                $params['is_repeat'] = 2;
            }
            if($params['service']=='1'){
                if ($model){
                    if ($model['admin_id']){
                        $params['admin_id'] = $model['admin_id'];
                    }else{
                        $admin_id = owner();
                        $params['admin_id'] = $admin_id;
                    }
                }else{
                    $admin_id = owner();
                    $params['admin_id'] = $admin_id;
                }
            }else{
                $params['admin_id'] = 5;
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }


    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    Hook::listen('precreen_edit_success',$ids);
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function examine($ids){

        $data = $this->model->find($ids);
        //判断邮箱是否存在用户表中
        $user = User::where('email',$data['email'])->find();

        //添加操作记录
        $Notes = [
            'form_id'=>$ids,
            'email'=>$data['email'],
            'admin_id'=>$this->auth->id,
        ];
        if ($user){
            //修改状态为3 重复提交
            $res = $this->model->where('id',$ids)->update(['status'=>3]);
            //添加操作记录
            $Notes['content'] ='System: Form duplicate submission' ;
            $Notes['caozuo'] = 3 ;
            Notes::create($Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return json(['code'=>2,'msg'=>'This email has already been registered','data'=>$res]);

        }

        //添加用户账号
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $password = '123456';
        $email = $data['email'];
        $mobile = $data['mobile'];
        $type = 2;

        $auth = new Auth();
        $res = $auth->register($first_name,$last_name, $password, $email, $mobile,$type);
        if (!$res) {
            $msg = $auth->getError();
            //添加操作记录
            $Notes['content'] ='System:' . $msg ;
            $Notes['caozuo'] = 1 ;
            Notes::create($Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return json(['code'=>3,'msg'=>$msg,'data'=>$res]);
        }else{

            //添加代母其他信息
            $api = [
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
            User::where('email',$email)->update($api);
        }

        //修改预审核表状态 为审核通过
        $this->model->where('id',$ids)->update(['status'=>1]);
        //审核通过，修改note表状态都为2；
        Notes::where('form_id',$ids)->update(['contact_status'=>2]);
        $uid = User::where('email',$data['email'])->order('createtime','desc')->value('id');
        $pre_screen = Prescreen::where('uid',$uid)->find();
        $red = [];
        if (!$pre_screen){
            $red['uid'] = $uid;
            $red['first_name'] = $data['first_name'];
            $red['middle_name'] = $data['middle_name'];
            $red['last_name'] = $data['last_name'];
            $red['birthday_time'] = $data['birthday_time'];
            $red['address'] = $data['address'];
            $red['city'] = $data['city'];
            $red['state'] = $data['state'];
            $red['postal_code'] = $data['postal_code'];
            $red['email'] = $data['email'];
            $red['mobile'] = $data['mobile'];
            $red['age'] = $data['age'];
            $red['height_ft'] = $data['height_ft'];
            $red['height_in'] = $data['height_in'];
            $red['bmi'] = $data['bmi'];
            $red['weight'] = $data['weight'];
            $red['marital_status'] = $data['marital_status'];
            $red['is_us'] = $data['is_us'];
            $red['product_number'] = $data['product_number'];
            $red['caesarean_number'] = $data['caesarean_number'];
            $red['miscarriage_number'] = $data['miscarriage_number'];
            $red['abortion_number'] = $data['abortion_number'];
            $red['is_abortion_reason'] = $data['is_abortion_reason'];
            $red['abortion_reason'] = $data['abortion_reason'];
            $red['is_complications_of_pregnancy'] = $data['is_complications_of_pregnancy'];
            $red['complications_of_pregnancy'] = $data['complications_of_pregnancy'];
            $red['contraceptive_measures'] = $data['contraceptive_measures'];
            $red['about_content'] = $data['about_content'];

            //判断表单完成进度
           if ($data['lead_source']=='vip.dopusa.com'){
               $red['status'] = 1;
               $red['form_complate_number'] = 22;
           }else{
               $red['form_complate_number'] = 4;
           }


            $res = Prescreen::create($red);
            if ($res){
                //发送邮件 代母审核通过
                $admin = $this->auth->username;
                $bcc_email = $this->auth->email;
                $uid = $this->auth->id;
                $mail = $data['email'] ;
//              $title = '恭喜您的代母预筛选通过了';
                $title = 'Congratulations! You’ve passed the pre-screening. Please continue with the application!';
                $type = 1 ;
                $pass ='123456';
                $username = $first_name.$last_name;

                $ret = send_email($admin,$uid,$mail,$title,$type,$pass,$username,$bcc_email);

                if ($ret['code'] !=1){
                    //添加操作记录
                    $Notes['content'] ='System:Sending email failed' ;
                    $Notes['caozuo'] = 1 ;
                    Notes::create($Notes);
                    $this->model->where('id',$ids)->setInc('follow_ups');
                    return  json(['code'=>1,'msg'=>'Sending email failed','data'=>$res]);
                }

                //添加操作记录
                $Notes['content'] ='System:Email sent successfully and approved' ;
                $Notes['caozuo'] = 1 ;
                Notes::create($Notes);
                $this->model->where('id',$ids)->setInc('follow_ups');

                return  json(['code'=>1,'msg'=>'Audit successful','data'=>$res]);
            }else{
                //添加操作记录
                $Notes['content'] ='System:Adding Prescreen table failed' ;
                $Notes['caozuo'] = 1 ;
                Notes::create($Notes);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return  json(['code'=>4,'msg'=>'Adding Prescreen table failed','data'=>$res]);
            }

        }else{
            //添加操作记录
            $Notes['content'] ='System:Prescreen table data already exists' ;
            $Notes['caozuo'] = 1 ;
            Notes::create($Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return json(['code'=>5,'msg'=>'Prescreen table data already exists','data'=>$res]);
        }




    }
    public function refuse(){

       $params = input();
       $ids = $params['ids'];
       $dq_content = $params['dq_content'];




        $data = $this->model->find($ids);
        $res = $this->model->where('id',$ids)->update(['status'=>2,'dq_content'=>$dq_content]);

        //添加操作记录
        $Notes = [
            'form_id'=>$ids,
            'email'=>$data['email'],
            'admin_id'=>$this->auth->id,
        ];

        if ($res){
            //发送邮件
            $admin = $this->auth->username;
            $bcc_email = $this->auth->email;
            $uid = $this->auth->id;
            $mail = $data['email'] ;
//            $title = '抱歉您的代母审核没有通过';
            $title = 'Sorry, your surrogacy application is not passed!';
            $type =2 ;
            $pass ='123456';
            $username = $data['first_name'].$data['last_name'];
            $ret = send_email($admin,$uid,$mail,$title,$type,$pass,$username,$bcc_email);

            if ($ret['code'] ==1){
                //添加操作记录
                $Notes['content'] = $dq_content;
                $Notes['caozuo'] = 2 ;
                Notes::create($Notes);
                Notes::where('form_id',$ids)->update(['contact_status'=>2]);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return  json(['code'=>1,'msg'=>'Rejected successfully, email sent successfully','data'=>$res]);
            }else{
                //添加操作记录
                $Notes['content'] = 'System:Rejected successfully, failed to send email';
                $Notes['caozuo'] = 2 ;
                Notes::create($Notes);
                Notes::where('form_id',$ids)->update(['contact_status'=>2]);
                $this->model->where('id',$ids)->setInc('follow_ups');
                return  json(['code'=>1,'msg'=>'Rejected successfully, failed to send email','data'=>$res]);
            }

        }else{
            $Notes['content'] = 'System:Rejection failed';
            $Notes['caozuo'] = 2 ;
            Notes::create($Notes);
            $this->model->where('id',$ids)->setInc('follow_ups');
            return  json(['code'=>2,'msg'=>'Rejection failed']);
        }

    }


    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $this->success("Ajax请求成功", null, ['id' => $ids]);
        }
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }


    public function fenpei(){
        $params = input();

        $admin_id = $params['category'];
        $form_ids = $params['ids'];
        $form_ids = explode(',',$form_ids);
        foreach ($form_ids as $v){
//            $model = $this->model->find($v);
//            if ($model['admin_id']){
//                return json(['code'=>3,'msg'=>'There are already followers, please do not reassign','res'=>$model]);
//            }
            $res = $this->model->where('id',$v)->update(['admin_id'=>$admin_id]);
        }
        if ($res){
            return json(['code'=>1,'msg'=>'Assigned successfully','res'=>$res]);
        }else{
            return json(['code'=>0,'msg'=>'Allocation failure']);
        }




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
//    public function notes_add(){
//
//
//        $params = input();
//
//        $params['admin_id'] = $this->auth->id;
//        if ($params['nexttime']){
//            $params['nexttime'] = strtotime($params['nexttime']);
//        }
//
//        if ($params['nexttime']){
//            $where = [
//                'form_id'=>$params['form_id']
//            ];
//            $model = Notes::where($where)->whereNotNull('nexttime')->order('createtime','desc')->find();
//            if ($model&&$model['createtime']){
//                $time_duan = time() -$model['createtime'];
//                if ($time_duan<300&&$model['content']==$params['content']){
//                    return json(['code'=>3,'msg'=>"Do not submit the same follow-up record within five minutes"]);
//                }
//                //修改联系状态为2，不需要联系
//                Notes::where($where)->where('nexttime','<',time())->update(['contact_status'=>2]);
//            }
//
//            $res = Notes::create($params);
//            //修改预筛选表状态
//
//            \app\admin\model\PreScreenForm::where('id',$params['form_id'])->update(['status'=>4]);
//
//        }else{
//            $where = [
//                'form_id'=>$params['form_id']
//            ];
//            $model = Notes::where($where)->whereNull('nexttime')->order('createtime','desc')->find();
//            if ($model&&$model['createtime']) {
//                $time_duan = time() - $model['createtime'];
//                if ($time_duan < 300 && $model['content'] == $params['content']) {
//                    return json(['code' => 3, 'msg' => "Do not submit the same follow-up record within five minutes"]);
//                }
//            }
//            $res = Notes::create($params);
//        }
//
//        if ($res){
//            \app\admin\model\PreScreenForm::where('id',$params['form_id'])->setInc('follow_ups',1);
//
//            return json(['code'=>1,'msg'=>"Success"]);
//        }else{
//            return json(['code'=>2,'msg'=>"Fail"]);
//        }
//
//
//
//
//
//
//    }

    public function notes_add(){

        $params = input();
        $params['admin_id'] = $this->auth->id;
        if ($params['nexttime']){
            $params['nexttime'] = strtotime($params['nexttime']);
        }
        $status = $params['caozuo'];

        if ($status =='4'){
            $params['contact_status']= '1';
        }else{
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
            \app\admin\model\PreScreenForm::where('id',$params['form_id'])->setInc('follow_ups',1);
            \app\admin\model\PreScreenForm::where('id',$params['form_id'])->update(['status'=>$status]);
            return json(['code'=>1,'msg'=>"Success"]);
        }else{
            return json(['code'=>2,'msg'=>"Fail"]);
        }

    }
    public function repeat(){
        $params = input();
        $email = $params['email'];
        if ($email){
            $count = $this->model->where('email',$email)->count();
            if ($count>1){
                return json(['code'=>1,'msg'=>'重复']);
            }else{
                return json(['code'=>2,'msg'=>'不重复']);
            }

        }else{
            return json(['code'=>3,'msg'=>'数据不存在']);
        }
    }

    public function status_contact(){
        date_default_timezone_set('PRC');
        $time= strtotime(date('Y-m-d 23:59:59', time()));
        $form_ids = Notes::where('contact_status',1)->where('nexttime','<',$time)
            ->group('form_id')
            ->order('createtime','desc')->column('form_id');
//        dump($time);die;
        if ($form_ids){
            $form_ids = implode(',',$form_ids);
            return json(['code'=>1,'data'=>$form_ids]);
        }else{

            return json(['code'=>2,'data'=>'']);
        }

    }
    
    public function note_del(){
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

}
