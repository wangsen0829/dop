<?php

namespace app\api\controller;

use app\admin\model\Admin;
use app\admin\model\DonorLead;
use app\admin\model\Export;
use app\admin\model\NewLead;
use app\admin\model\PreScreenForm;
use app\admin\validate\SurrogacyLead;
use app\common\controller\Api;
use JetBrains\PhpStorm\NoReturn;
use think\Config;
use think\Model;
use think\Request;
use think\Validate;

/**
 * 示例接口
 */
class Surrogacy extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    //广告
    public function add_adver(){
        $params = input();
        if (!$params){
            return json(['code'=>3,'msg'=>'上传数据不能为空']);
        }
        //数据转化
        $service = $params['service'];
        $service = Config::get('site.service')[$service];
        $where = [
            'email'=>$params['email'],
            'service'=>$service,
        ];
        $new_lead_model = NewLead::where($where)->find();

        if (isset($params['message'])){
            $message = $params['message'];
        }else{
            $message = '';
        }
        if (isset($params['referral'])){
            $referral = $params['referral'];
        }else{
            $referral = '';
        }

        if($service=='1'){
            if ($new_lead_model){
                if ($new_lead_model['admin_id']){
                    $admin_id= $new_lead_model['admin_id'];
                }else{
                    $admin_id = owner();
                }
            }else{
                $admin_id = owner();
            }
        }else{
            $admin_id = 5;
        }

        //实例化NewLead模型
        $Newlead = new NewLead();
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['phone'];
        //状态
        $Newlead->service = $service;
        $Newlead->type = 3; //广告添加
        $Newlead->admin_id = $admin_id;
        $Newlead->referral = $referral;
        $Newlead->is_repeat = $new_lead_model?2:1;

        //API information
        $Newlead->media = $params['media'];
        $Newlead->account = $params['account'];
        $Newlead->campaign = $params['campaign'];
        $Newlead->adgroup = $params['adgroup'];
        $Newlead->adname = $params['adname'];
        $Newlead->message = $message;

        if ($referral){
            $Newlead->lead_source = Config::get('site.lead_source')[3];
        }else{
            $Newlead->lead_source = $params['lead_source'];;
        }
        if ($service==1){
            $surrogacy = [];
            $Newlead->surrogacy = $surrogacy; //重点
            $res = $Newlead->together('surrogacy')->save();
        }elseif ($service==2){
            $donor = [];
            $Newlead->donor = $donor; //重点
            $res = $Newlead->together('donor')->save();
        }else{
            $res = $Newlead->save();
        }
        if ($res){
            return json(['code'=>1,'msg'=>'添加成功','res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>'添加失败']);
        }
    }

    //contact 联系页面
    public function add_contact(){
        $params = input();
        if (!$params){
            return json(['code'=>3,'msg'=>'上传数据不能为空']);
        }
        //数据转化
        $service = $params['service'];
        $service = Config::get('site.service')[$service];
        $where = [
            'email'=>$params['email'],
            'service'=>$service,
        ];
        $new_lead_model = NewLead::where($where)->find();
        if($service=='1'){
            if ($new_lead_model){
                if ($new_lead_model['admin_id']){
                    $admin_id= $new_lead_model['admin_id'];
                }else{
                    $admin_id = owner();
                }
            }else{
                $admin_id = owner();
            }
        }else{
            $admin_id = 5;
        }

        //实例化NewLead模型
        $Newlead = new NewLead();
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['mobile'];
        //状态
        $Newlead->service = $service;
        $Newlead->type = 2; //表单提交
        $Newlead->admin_id = $admin_id;
        $Newlead->is_repeat = $new_lead_model?2:1;
        $Newlead->lead_source = Config::get('site.lead_source')[5];

        if ($service==1){
            $surrogacy = [];
            $Newlead->surrogacy = $surrogacy; //重点
            $res = $Newlead->together('surrogacy')->save();
        }elseif ($service==2){
            $donor = [];
            $Newlead->donor = $donor; //重点
            $res = $Newlead->together('donor')->save();
        }else{
            $res = $Newlead->save();
        }
        if ($res){
            return json(['code'=>1,'msg'=>'添加成功','res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>'添加失败']);
        }
    }

    //代母表添加
    public function add_surrogacy(){
        $params = input();
        if (!$params){
            return json(['code'=>3,'msg'=>'上传数据不能为空']);
        }
        $where = [
            'email'=>$params['email'],
            'service'=>1,
        ];
        //判断线索是否存在（邮箱）
        $new_lead_model = NewLead::where($where)->find();

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

        //实例化NewLead模型
        $Newlead = new NewLead();
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->middle_name = $params['middle_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['mobile'];
        //状态
        $Newlead->service = Config::get('site.service')['Be a Surrogate'];
        $Newlead->type = 2; //表单添加
        $Newlead->admin_id = $admin_id;
        $Newlead->is_repeat = $new_lead_model?2:1;
        //API information
        $Newlead->lead_source = Config::get('site.lead_source')[5];

        $bmi = Null;
        if ($params['height_ft']&&$params['height_in']){
            $ft = $params['height_ft'];
            $in = $params['height_in'];
            $lbs = $params['weight'];
            $height = ($ft*12)+$in;
            $height = $height*$height;
            $bmi = ($lbs/$height)*703;
            $bmi = round($bmi);
        }
//        实例化SurrogacyLead模型 关联表surrogacy_lead
        $surrogacy = [
            "birthday_time" =>$params['birthday_time'] ,
            "address" =>$params['address'] ,
            "city" =>$params['city'] ,
            "state" =>$params['state'] ,
            "postal_code" =>$params['postal_code'] ,
            "age" =>$params['age']?$params['age']:NULL,
            "height_ft" =>$params['height_ft'] ,
            "height_in" =>$params['height_in'] ,
            "bmi" =>$bmi ,
            "weight" =>$params['weight']?$params['weight']:NULL,
            "marital_status" =>$params['marital_status']?($params['marital_status']=='Yes'?1:2): NULL ,
            "is_us" =>$params['is_us']?($params['is_us']=='Yes'?1:2): NULL,
            "product_number" =>$params['product_number']?$params['product_number']:NULL,
            "caesarean_number" =>$params['caesarean_number']?$params['caesarean_number']:NULL,
            "miscarriage_number" =>$params['miscarriage_number']?$params['miscarriage_number']:NULL,
            "abortion_number" =>$params['abortion_number']?$params['abortion_number']:NULL,
            "is_abortion_reason" =>$params['is_abortion_reason']?($params['is_abortion_reason']=='Yes'?1:2): NULL ,
            "abortion_reason" =>$params['abortion_reason'] ,
            "is_complications_of_pregnancy" =>$params['is_complications_of_pregnancy']?($params['is_complications_of_pregnancy']=='Yes'?1:2): NULL ,
            "complications_of_pregnancy" =>$params['complications_of_pregnancy'] ,
            "contraceptive_measures" =>$params['contraceptive_measures'] ,
            "about_content" =>$params['about_content'] ,
        ];
        $Newlead->surrogacy = $surrogacy; //重点
        $res = $Newlead->together('surrogacy')->save();
        if ($res){
            return json(['code'=>1,'msg'=>'添加成功','res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>'添加失败']);
        }
    }

    //捐卵添加
    public function add_donor(){
        $params = input();
        if (!$params){
            return json(['code'=>3,'msg'=>'上传数据不能为空']);
        }
        $where = [
            'email'=>$params['email'],
            'service'=>2,
        ];
        //判断线索是否存在（邮箱）
        $new_lead_model = NewLead::where($where)->find();

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

        //实例化NewLead模型
        $Newlead = new NewLead();
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->middle_name = $params['middle_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['mobile'];
        //状态
        $Newlead->service = Config::get('site.service')['Be a Surrogate'];
        $Newlead->type = 2; //表单添加
        $Newlead->admin_id = $admin_id;
        $Newlead->is_repeat = $new_lead_model?2:1;
        //API information
        $Newlead->lead_source = Config::get('site.lead_source')[5];

        $bmi = Null;
        if ($params['height_ft']&&$params['height_in']){
            $ft = $params['height_ft'];
            $in = $params['height_in'];
            $lbs = $params['weight'];
            $height = ($ft*12)+$in;
            $height = $height*$height;
            $bmi = ($lbs/$height)*703;
            $bmi = round($bmi);
        }
//        实例化SurrogacyLead模型 关联表surrogacy_lead
        $surrogacy = [
            "birthday_time" =>$params['birthday_time'] ,
            "address" =>$params['address'] ,
            "city" =>$params['city'] ,
            "state" =>$params['state'] ,
            "postal_code" =>$params['postal_code'] ,
            "age" =>$params['age']?$params['age']:NULL,
            "height_ft" =>$params['height_ft'] ,
            "height_in" =>$params['height_in'] ,
            "bmi" =>$bmi ,
            "weight" =>$params['weight']?$params['weight']:NULL,
            "marital_status" =>$params['marital_status']?($params['marital_status']=='Yes'?1:2): NULL ,
            "is_us" =>$params['is_us']?($params['is_us']=='Yes'?1:2): NULL,
            "product_number" =>$params['product_number']?$params['product_number']:NULL,
            "caesarean_number" =>$params['caesarean_number']?$params['caesarean_number']:NULL,
            "miscarriage_number" =>$params['miscarriage_number']?$params['miscarriage_number']:NULL,
            "abortion_number" =>$params['abortion_number']?$params['abortion_number']:NULL,
            "is_abortion_reason" =>$params['is_abortion_reason']?($params['is_abortion_reason']=='Yes'?1:2): NULL ,
            "abortion_reason" =>$params['abortion_reason'] ,
            "is_complications_of_pregnancy" =>$params['is_complications_of_pregnancy']?($params['is_complications_of_pregnancy']=='Yes'?1:2): NULL ,
            "complications_of_pregnancy" =>$params['complications_of_pregnancy'] ,
            "contraceptive_measures" =>$params['contraceptive_measures'] ,
            "about_content" =>$params['about_content'] ,
        ];
        $Newlead->surrogacy = $surrogacy; //重点
        $res = $Newlead->together('surrogacy')->save();
        if ($res){
            return json(['code'=>1,'msg'=>'添加成功','res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>'添加失败']);
        }
    }


    public function ws(){

        $data = PreScreenForm::order('id','desc')->select();
        foreach ($data as $v){
            $createtime = $v['createtime'];
            $updatetime = $v['updatetime'];
            $deletetime = $v['deletetime'];
            $lead =[
                //基础信息
                'id'=>$v['id'],
                'first_name'=>$v['first_name'],
                'last_name'=>$v['last_name'],
                'middle_name'=>$v['middle_name'],
                'email'=>$v['email'],
                'mobile'=>$v['mobile'],
                //状态
                'service'=>$v['service'],
                'status'=>$v['status'],
                'type'=>$v['type'],
                'admin_id'=>$v['admin_id'],
                'lead_source'=>$v['lead_source'],

                'referral'=>$v['referral'],
                'referral_id'=>$v['referral_id'],
                'is_repeat'=>$v['is_repeat'],
                //API information
                'media'=>$v['media'],
                'account'=>$v['account'],
                'campaign'=>$v['campaign'],
                'adgroup'=>$v['adgroup'],
                'adname'=>$v['adname'],
                'message'=>$v['message'],
                //other
                'follow_ups'=>$v['follow_ups'],
                'old_status'=>$v['old_status'],
                'dq_content'=>$v['dq_content'],
                'createtime'=>$createtime,
                'updatetime'=>$updatetime,
                'deletetime'=>$deletetime,
            ] ;


            if ($v['service']==1){
                $surrogacy = [];
                $surrogacy['form_id'] = $v['id'];
                $surrogacy['birthday_time'] = $v['birthday_time'];
                $surrogacy['address'] = $v['address'];
                $surrogacy['city'] = $v['city'];
                $surrogacy['state'] = $v['state'];
                $surrogacy['postal_code'] = $v['postal_code'];
                $surrogacy['age'] = $v['age'];
                $surrogacy['height_ft'] = $v['height_ft'];
                $surrogacy['height_in'] = $v['height_in'];
                $surrogacy['bmi'] = $v['bmi'];
                $surrogacy['weight'] = $v['weight'];
                $surrogacy['marital_status'] = $v['marital_status'];
                $surrogacy['is_us'] = $v['is_us'];
                $surrogacy['product_number'] = $v['product_number'];
                $surrogacy['caesarean_number'] = $v['caesarean_number'];
                $surrogacy['miscarriage_number'] = $v['miscarriage_number'];
                $surrogacy['abortion_number'] = $v['abortion_number'];
                $surrogacy['is_abortion_reason'] = $v['is_abortion_reason'];
                $surrogacy['abortion_reason'] = $v['abortion_reason'];
                $surrogacy['is_complications_of_pregnancy'] = $v['is_complications_of_pregnancy'];
                $surrogacy['complications_of_pregnancy'] = $v['complications_of_pregnancy'];
                $surrogacy['contraceptive_measures'] = $v['contraceptive_measures'];
                $surrogacy['about_content'] = $v['about_content'];
                $surrogacy['createtime'] =$createtime;
                $surrogacy['updatetime'] = $updatetime;
                $surrogacy['deletetime'] = $deletetime;
                \app\admin\model\SurrogacyLead::create($surrogacy);
            }elseif ($v['service']==2){
                $donor = [];
                $donor['form_id'] = $v['id'];
                $donor['createtime'] = $createtime;
                $donor['updatetime'] = $updatetime;
                $donor['deletetime'] = $deletetime;
                \app\admin\model\DonorLead::create($donor);
            }
            $res =  \app\admin\model\NewLead::create($lead);

        }
        if ($res){
            return json(['code'=>1,'msg'=>'添加成功','res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>'添加失败']);
        }
    }
}
