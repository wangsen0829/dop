<?php

namespace app\admin\controller;

use app\admin\model\AboutSurrogacy;
use app\admin\model\AdditionalInformation;
use app\admin\model\AdminLog;
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
use app\admin\model\Background;
use app\admin\model\SurrogacySbp;
use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Request;
use think\Session;
use think\Validate;

/**
 * 代母详情
 * @internal
 */
class Surrogacy extends Backend
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    public function pre_screen(){
        $this->view->engine->layout(false);
        $uid = input('uid');
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
            $uid = $params['uid'];

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
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];
            $params['birthday_time'] = $this->strtotime($params['birthday_time']);

            if ($params['height_ft']&&$params['height_in']){
                $ft = $params['height_ft'];
                $in = $params['height_in'];
                $lbs = $params['weight'];
                $height = ($ft*12)+$in;
                $height = $height*$height;
                $bmi = ($lbs/$height)*703;
                $params['bmi'] = round($bmi);
            }

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

        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("bir_time",$bir_time?$bir_time:0);

        return $this->view->fetch();
    }

    public function personal_info(){
        $this->view->engine->layout(false);

        $uid = input('uid');
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
            $uid = $params['uid'];
            $model = PersonalInfo::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                PersonalInfo::where('uid',$uid)->update($params);
            }else{
                PersonalInfo::create($params);
            }
            $this->success('successful');
        }


        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }



    public function  surrogate_photo(){
        $this->view->engine->layout(false);

        $uid = input('uid');
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
            $uid = $params['uid'];
//            dump($params);die;
            $model = SurrogatePhoto::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
            }
            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                SurrogatePhoto::where('uid',$uid)->update($params);
            }else{
                SurrogatePhoto::create($params);
            }
            $this->success('ok');
        }

        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function obstetric_history(){
        $this->view->engine->layout(false);

        $uid = input('uid');

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
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
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
                $baby_ids = [];
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
        $uid = input('uid');
        $row = MedicalInformation::where('uid',$uid)->find();
        $row = $row?$row->toArray():$row;
        // 表单总数
        $form_number = FormNumber::where('name','medical_information')->value('number');
        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
            if ($row['any_diseases']){
                $row['any_diseases'] = explode(',',$row['any_diseases']);
            }else{
                $row['any_diseases']  = array();
            }
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
//            dump($params);die;
            $uid = $params['uid'];
            if (isset($params['any_diseases'])){
                $params['any_diseases'] = implode(',',$params['any_diseases']);
            }else{
                $params['any_diseases'] = '';
            }
//            dump($params);die;
            $model = MedicalInformation::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                MedicalInformation::where('uid',$uid)->update($params);
            }else{
                MedicalInformation::create($params);
            }
            $this->success('successful');
        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function about_surrogacy(){

        $this->view->engine->layout(false);
        $uid = input('uid');
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
//            dump($params);die;
            $uid = $params['uid'];
            $model = AboutSurrogacy::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
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


        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function other_information(){
        $this->view->engine->layout(false);

        $uid = input('uid');
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
            $uid = $params['uid'];
            $model = OtherInformation::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                OtherInformation::where('uid',$uid)->update($params);
            }else{
                OtherInformation::create($params);
            }
            $this->success('successful');
        }

        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function additional_information(Request $request){
        $uid = input('uid');
        $row = AdditionalInformation::where('uid',$uid)->find();
        // 表单总数
        $form_number = FormNumber::where('name','additional_information')->value('number');

        if ($row){
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();
            $model = AdditionalInformation::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
            }

            $params['form_complate_number'] = $form_number - $params['form_complate_number'];

            if ($model){
                AdditionalInformation::where('uid',$uid)->update($params);
            }else{
                AdditionalInformation::create($params);
            }
            $this->success('successful','index/user/health_record_release');
        }

        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->engine->layout(false);
        return $this->view->fetch();
    }

    public function health_record_release(){
        $this->view->engine->layout(false);
        $uid = input('uid');
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
            $lv = $row['form_complate_number']/$form_number*100;
        }else{
            $lv = 0;
        }

        if ($this->request->isPost()) {
            $params = $this->request->post();
            $uid = $params['uid'];
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
                $baby_ids =[];
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

                    $where= [];
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

            $model = HealthRecordRelease::where('uid',$uid)->find();
//            表单全部填写完成
            if ($params['form_complate_number']=='0'){
               $params['status'] = '1';
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
                'form_complate_number'=>$params['form_complate_number'],
                'status'=>$params['status'],
            ];

            if ($model){
                HealthRecordRelease::where('uid',$uid)->update($data);
            }else{
                HealthRecordRelease::create($data);
            }





            $this->success('successful');
        }
//        dump($regnancy_informatiom_data);die;
        $this->view->assign("form_number", $form_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);
        $this->view->assign("regnancy_informatiom_data", $regnancy_informatiom_data);
        $this->view->assign("button_number", $button_number);

        return $this->view->fetch();
    }

    public function background(){
        $this->view->engine->layout(false);

        $uid = input('uid');
        $row = Background::where('uid',$uid)->find();
        if ($row){
            $form_complate_number = $row['form_complate_number'];
        }else{
            $form_complate_number = 0 ;
        }
        // 表单总数
        $form_number = FormNumber::where('name','background')->value('number');
        if ($row){
            $lv = $form_complate_number/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if (!$params['file']||!$params['bid']){
                $this->error('File upload cannot be empty');
            }
            $params['status'] = '1';
            $params['form_complate_number'] = 2;
            $model = Background::where('uid',$params['uid'])->find();
            if ($model){
                Background::where('uid',$params['uid'])->update($params);
            }else{
                Background::create($params);
            }
            $this->success('successful');
        }

        $this->view->assign("form_number", $form_number);
        $this->view->assign("form_complate_number",$form_complate_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function sbp(){
        $this->view->engine->layout(false);

        $uid = input('uid');
        $row = SurrogacySbp::where('uid',$uid)->find();
        if ($row){
            $form_complate_number = $row['form_complate_number'];
        }else{
            $form_complate_number = 0;
        }
        // 表单总数
        $form_number = FormNumber::where('name','sbp')->value('number');



        if ($row){
            $lv = $form_complate_number/$form_number*100;
        }else{
            $lv = 0;
        }
        if ($this->request->isPost()) {
            $params = $this->request->post();

            if (!$params['file']){
                $this->error('File upload cannot be empty');
            }
            $params['status'] = '1';
            $params['form_complate_number'] = 1;
            $model = SurrogacySbp::where('uid',$params['uid'])->find();
            if ($model){
                SurrogacySbp::where('uid',$params['uid'])->update($params);
            }else{
                SurrogacySbp::create($params);
            }
            $this->success('successful');
        }

        $this->view->assign("form_number", $form_number);
        $this->view->assign("form_complate_number",$form_complate_number);
        $this->view->assign("lv", $lv);
        $this->view->assign("uid", $uid);
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    public function medical_fax(){
        $this->view->engine->layout(false);
        $uid = input('uid');
        $row = RegnancyInformation::where('uid',$uid)->select();
        $form_number = count($row)*2;
        $form_complate_number = 0;
        if ($row){
            foreach ($row as $v){
                if ($v['ob_file']){
                    $form_complate_number++;
                }
                if ($v['delivery_file']){
                    $form_complate_number++;
                }
            }
        }
        if ($form_number){
            $lv = $form_complate_number/$form_number*100;
        }else{
            $lv = 0;
        }


        if ($this->request->isPost()) {

        }
        $this->view->assign("form_number", $form_number);
        $this->view->assign("form_complate_number", $form_complate_number);
        $this->view->assign("row", $row);
        $this->view->assign("ids", $uid);
        $this->view->assign("lv", $lv);
        return $this->view->fetch();
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

}
