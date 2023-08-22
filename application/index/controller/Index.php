<?php

namespace app\index\controller;
use app\admin\model\SurrogacyLead;
use app\admin\model\Admin;
use app\admin\model\NewLead;
use app\admin\model\TemplateEmail;
use app\common\controller\Frontend;
use think\Config;
use app\admin\model\Api;

use app\common\library\Email;
class Index extends Frontend
{
    protected $relationSearch = true;
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';


    public function index($aff='')
    {
        $this->view->assign("aff", $aff);
        return $this->view->fetch();
    }

    public function thank($aff=''){
        $this->view->assign("aff", $aff);
        return $this->view->fetch();
    }

    public function pre_screen(){
        $params = $this->request->post("row/a");
        $surrogacy = $this->request->post("surrogacy/a");
        //判断线索是否存在（邮箱）
        $service = Config::get('site.service')['Be a Surrogate'];
        $where = [
            'email'=>$params['email'],
            'service'=>$service,
        ];
        $new_lead_model = NewLead::where($where)->find();
        //数据转化
        //二维码推荐人
        $ref_id = $params['referral_id'];
        $referral_id = null;
        $referral = null;
        if ($ref_id){
            $admin = Admin::find($ref_id);
            if ($admin){
                $admin_id = $admin['id'];
                $referral = $admin['username'];
                $referral_id = $ref_id;
            }
        }else{
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
        $Newlead->service = $service;
        $Newlead->type = 2; //表单添加
        $Newlead->admin_id = $admin_id;
        $Newlead->referral = $referral;
        $Newlead->referral_id = $referral_id;
        $Newlead->is_repeat = $new_lead_model?2:1;
        //API information
        if ($ref_id){
            $Newlead->lead_source = Config::get('site.lead_source')[3];
        }else{
            $Newlead->lead_source = Config::get('site.lead_source')[6];
        }
        $bmi = Null;
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
        if ($res){
            $this->redirect('index/index/thank');
        }else{
            $this->error('fail');
        }
    }


    //捐卵
    public function donor($aff='')
    {
        $this->view->assign("aff", $aff);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $donor = $this->request->post("donor/a");
            //判断线索是否存在（邮箱）
            $service = Config::get('site.service')['Be an Egg Donor'];
            $where = [
                'email'=>$params['email'],
                'service'=>$service,
            ];
            $new_lead_model = NewLead::where($where)->find();
            //数据转化
            //二维码推荐人
            $ref_id = $params['referral_id'];
            $referral_id = null;
            $referral = null;
            if ($ref_id){
                $admin = Admin::find($ref_id);
                if ($admin){
                    $admin_id = $admin['id'];
                    $referral = $admin['username'];
                    $referral_id = $ref_id;
                }
            }else{
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
            }

            //实例化NewLead模型
            $Newlead = new NewLead();
            //基础信息
            $Newlead->service = $service;
            $Newlead->first_name = $params['first_name'];
            $Newlead->last_name = $params['last_name'];
            $Newlead->email = $params['email'];
            $Newlead->mobile = $params['mobile'];
            $Newlead->type = 2; //表单添加
            $Newlead->admin_id = $admin_id;
            $Newlead->referral = $referral;
            $Newlead->referral_id = $referral_id;
            $Newlead->is_repeat = $new_lead_model?2:1;
            //API information
            if ($ref_id){
                $Newlead->lead_source = Config::get('site.lead_source')[2];
            }else{
                $Newlead->lead_source = Config::get('site.lead_source')[6];
            }
            $bmi = Null;
            if ($donor['height_ft']&&$donor['height_in']){
                $ft = $donor['height_ft'];
                $in = $donor['height_in'];
                $lbs = $donor['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $bmi = round($bmi);
            }
            $donor['bmi'] = $bmi;
            $Newlead->donor = $donor; //重点
            $res = $Newlead->together('donor')->save();
            if ($res){
                $this->redirect('index/index/thank');
            }else{
                $this->error('fail');
            }
        }
        return $this->view->fetch();
    }

    //运世达邀请函
    public function yqh(){
        return $this->view->fetch();
    }
    //运世达邀请函
    public function canvas(){
        return $this->view->fetch();
    }


//    public function pre_screen(){
//        $params = $this->request->post("row/a");
//        $surrogacy = $this->request->post("surrogacy/a");
//        //判断线索是否存在（邮箱）
//        $new_lead_model = NewLead::where('email',$params['email'])->find();
//        //数据转化
//        //二维码推荐人
//        $ref_id = $params['referral_id'];
//        $referral_id = null;
//        $referral = null;
//        if ($ref_id){
//            $admin = Admin::find($ref_id);
//            if ($admin){
//                $admin_id = $admin['id'];
//                $referral = $admin['username'];
//                $referral_id = $ref_id;
//            }
//        }else{
//            //添加分配者
//            if ($new_lead_model){
//                if ($new_lead_model['admin_id']){
//                    $admin_id = $new_lead_model['admin_id'];
//                }else{
//                    $admin_id = owner();
//                }
//            }else{
//                $admin_id = owner();
//            }
//        }
//
//        //实例化NewLead模型
//        $Newlead = new NewLead();
//        //基础信息
//        $Newlead->first_name = $params['first_name'];
//        $Newlead->middle_name = $params['middle_name'];
//        $Newlead->last_name = $params['last_name'];
//        $Newlead->email = $params['email'];
//        $Newlead->mobile = $params['mobile'];
//        //状态
//        $Newlead->service = Config::get('site.service')['Be a Surrogate'];
//        $Newlead->type = 2; //表单添加
//        $Newlead->admin_id = $admin_id;
//        $Newlead->referral = $referral;
//        $Newlead->referral_id = $referral_id;
//        $Newlead->is_repeat = $new_lead_model?2:1;
//        //API information
//        if ($ref_id){
//            $Newlead->lead_source = Config::get('site.lead_source')[3];
//        }else{
//            $Newlead->lead_source = Config::get('site.lead_source')[6];
//        }
//        if ($surrogacy['height_ft']&&$surrogacy['height_in']){
//            $ft = $surrogacy['height_ft'];
//            $in = $surrogacy['height_in'];
//            $lbs = $surrogacy['weight'];
//            $height = ($ft*12)+$in;
//            $height = $height*$height;
//            $bmi = ($lbs/$height)*703;
//            $bmi = round($bmi);
//        }
//        $surrogacy['bmi'] = $surrogacy;
//        //实例化SurrogacyLead模型 关联表surrogacy_lead
////        $surrogacy = [
////            "birthday_time" =>$params['birthday_time'] ,
////            "address" =>$params['address'] ,
////            "city" =>$params['city'] ,
////            "state" =>$params['state'] ,
////            "postal_code" =>$params['postal_code'] ,
////            "age" =>$params['age'] ,
////            "height_ft" =>$params['height_ft'] ,
////            "height_in" =>$params['height_in'] ,
////            "bmi" =>$bmi ,
////            "weight" =>$params['weight'] ,
////            "marital_status" =>$params['marital_status'] ,
////            "is_us" =>$params['is_us'] ,
////            "product_number" =>$params['product_number'] ,
////            "caesarean_number" =>$params['caesarean_number'] ,
////            "miscarriage_number" =>$params['miscarriage_number'] ,
////            "abortion_number" =>$params['abortion_number'] ,
////            "is_abortion_reason" =>$params['is_abortion_reason'] ,
////            "abortion_reason" =>$params['abortion_reason'] ,
////            "is_complications_of_pregnancy" =>$params['is_complications_of_pregnancy'] ,
////            "complications_of_pregnancy" =>$params['complications_of_pregnancy'] ,
////            "contraceptive_measures" =>$params['contraceptive_measures'] ,
////            "about_content" =>$params['about_content'] ,
////        ];
//        $Newlead->surrogacy = $surrogacy; //重点
//        $res = $Newlead->together('surrogacy')->save();
//        if ($res){
//            $this->redirect('index/index/thank');
//        }else{
//            $this->error('fail');
//        }
//    }
}
