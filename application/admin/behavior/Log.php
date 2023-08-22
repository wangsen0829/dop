<?php

namespace app\admin\behavior;
use app\admin\model\Admin;
use app\admin\model\NewLead;
use app\admin\model\PreScreenForm;
use app\admin\model\User;
use app\admin\model\Notes;
use app\admin\model\Log as Logmodel;
use app\admin\library\Auth;
class Log
{
    public function run(&$params)
    {

    }

    //预筛选表修改
    public function precreenEditSuccess($params='')
    {
        $sql = PreScreenForm::getLastSql();
        $auth = Auth::instance();
        $admin_id = $auth->isLogin() ? $auth->id : 0;
        $username = $auth->isLogin() ? $auth->username : __('Unknown');
        $data = [
            'admin_id'=>$admin_id,
            'username'=>$username,
            'title'=>'预筛选表修改',
            'content'=>$sql,
            'url' => substr(request()->url(), 0, 1500),
        ];
        //添加log记录表
        Logmodel::create($data);

//        $model = PreScreenForm::find($params);
//
//        $form = [
//            'form_id'=>$model['id'],
//            'email'=>$model['email'],
//            'admin_id'=>$admin_id,
//            'content'=>$sql,
//            'caozuo'=>$model['status'],
////            'type'=>1,
//        ];
//        if ($model['status'] =='4'||$model['status']=='0'){
//            $form['contact_status']= '1';
//        }else{
//            $form['contact_status']= '2';
//            Notes::where('form_id',$model['id'])->update(['contact_status'=>'2']);
//        }
//        //添加notes表
//        Notes::create($form);
//        PreScreenForm::where('id',$model['id'])->setInc('follow_ups');
    }


    //预筛选表修改
    public function userEditSuccess($params='')
    {
        $sql = User::getLastSql();
        $auth = Auth::instance();
        $admin_id = $auth->isLogin() ? $auth->id : 0;
        $username = $auth->isLogin() ? $auth->username : __('Unknown');
        $data = [
            'admin_id'=>$admin_id,
            'username'=>$username,
            'title'=>'代母信息修改',
            'content'=>$sql,
            'url' => substr(request()->url(), 0, 1500),
        ];
        //添加log记录表
        Logmodel::create($data);
    }


    //添加操作记录
    public function notesAdd($params){
        $ids = $params['form_id'];
        $data = NewLead::find($ids);
        $auth = Auth::instance();
        $admin_id = $auth->isLogin() ? $auth->id : 0;
        $data = [
            'form_id'=>$ids,
            'email'=>$data['email'],
            'admin_id'=>$admin_id,
            'content'=>$params['content'],
            'caozuo'=>$params['caozuo'],
            'contact_status'=>$params['contact_status'],
        ];
        Notes::create($data);
    }

}
