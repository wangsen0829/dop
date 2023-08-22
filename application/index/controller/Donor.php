<?php

namespace app\index\controller;
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
use app\admin\model\FormNumber;
use app\common\controller\Frontend;
use think\Config;
use think\Request;


class Donor extends Frontend
{
    protected $relationSearch = true;
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';


    public function pre_screen(Request $request){

        $uid = $this->auth->id;
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
//            dump($params);die;
            $model = DonorPreScreen::where('uid',$uid)->find();

            //表单全部填写完成
            if ($params['form_complate_number']=='0'){
                $params['status'] =1;
            }
            $params['form_complate_number'] = $form_number - intval($params['form_complate_number']);
            if ($model){
                DonorPreScreen::where('uid',$uid)->update($params);
            }else{
                DonorPreScreen::create($params);
            }
            $this->success('successful');
        }
        $examine_pre_screen = 0;
        $examine = DonorExamine::where('uid',$uid)->find();
        if ($examine){
            $examine_pre_screen = $examine['pre_screen'];
        }
        $this->view->assign("examine_pre_screen",$examine_pre_screen);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function photos(){
        $uid = $this->auth->id;
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
        $examine_photos = 0;
        $examine = DonorExamine::where('uid',$uid)->find();
        if ($examine){
            $examine_photos = $examine['photos'];
        }
        $this->view->assign("examine_photos",$examine_photos);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function personal(){
        $uid = $this->auth->id;
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
            if ($params['form_complate_number']=='0'){
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
        $examine_personal = 0;
        $examine = DonorExamine::where('uid',$uid)->find();
        if ($examine){
            $examine_personal = $examine['personal'];
        }
        $this->view->assign("examine_personal",$examine_personal);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function education(){
        $uid = $this->auth->id;
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
        $examine_education = 0;
        $examine = DonorExamine::where('uid',$uid)->find();
        if ($examine){
            $examine_education = $examine['education'];
        }
        $this->view->assign("examine_education",$examine_education);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function character(){
        $uid = $this->auth->id;
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
        $examine_character = 0;
        $examine = DonorExamine::where('uid',$uid)->find();
        if ($examine){
            $examine_character = $examine['character'];
        }
        $this->view->assign("examine_character",$examine_character);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function medical(){
        $uid = $this->auth->id;
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
        $examine_medical = 0;
        $examine = DonorExamine::where('uid',$uid)->find();
        if ($examine){
            $examine_medical = $examine['medical'];
        }
        $this->view->assign("examine_medical",$examine_medical);
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("row_qinshu", $row_qinshu);
        $this->view->assign("row_qinshu_disease", $row_qinshu_disease);
        $this->view->assign("number", $number);

        return $this->view->fetch();
    }
}
