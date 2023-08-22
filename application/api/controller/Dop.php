<?php

namespace app\api\controller;

use app\admin\model\NewLead;
use app\common\controller\Api;
use think\Config;
use think\Model;
use think\Request;
use think\Validate;

/**
 * 示例接口
 */
class Dop extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    //代母表添加
    public function add(){
        $params = input();
        if (!$params){
            return json(['code'=>3,'msg'=>'上传数据不能为空']);
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

        //实例化NewLead模型
        $Newlead = new NewLead();
        //基础信息
        $Newlead->first_name = $params['first_name'];
        $Newlead->last_name = $params['last_name'];
        $Newlead->email = $params['email'];
        $Newlead->mobile = $params['mobile'];
        //状态
        $Newlead->service = Config::get('site.service')['Be an Egg Donor'];
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
//
        $donor = [
            "state" =>$params['state'] ,
            "age" =>$params['age']?$params['age']:NULL,
            "height_ft" =>$params['height_ft'] ,
            "height_in" =>$params['height_in'] ,
            "bmi" =>$bmi ,
            "weight" =>$params['weight']?$params['weight']:NULL,
            "ethnicity" =>$params['ethnicity'],
            "blood_type" =>$params['blood_type'],
            "place_of_birth" =>$params['place_of_birth'],
            "highest_education" =>$params['highest_education'],
            "occupation" =>$params['occupation'],
            "is_donated" =>$params['is_donated']?($params['is_donated']=='Yes'?1:2): NULL,
            "is_smoke" =>$params['is_smoke']?($params['is_smoke']=='Yes'?1:2): NULL,
            "is_drink" =>$params['is_drink']?($params['is_drink']=='Yes'?1:2): NULL,
            "is_illicit_drugs" =>$params['is_illicit_drugs']?($params['is_illicit_drugs']=='Yes'?1:2): NULL,
            "is_any_medications" =>$params['is_any_medications']?($params['is_any_medications']=='Yes'?1:2): NULL,
            "abortion_reason" =>$params['abortion_reason']
        ];
        $Newlead->donor = $donor; //重点
        $res = $Newlead->together('donor')->save();
        if ($res){
            return json(['code'=>1,'msg'=>'添加成功','res'=>$res]);
        }else{
            return json(['code'=>2,'msg'=>'添加失败']);
        }
    }
}
