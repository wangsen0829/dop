<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class PreScreenForm extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'pre_screen_form';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'marital_status_text',
        'birthday_time_text',
        'status_text'
    ];
    

    
    public function getMaritalStatusList()
    {
//        return ['0' => __('Marital_status 0'), '1' => __('Marital_status 1'), '2' => __('Marital_status 2'), '3' => __('Marital_status 3')];
        return [ '1' => 'Yes', '2' => 'No'];
    }

    public function getStatusList()
    {
        return ['0' => __('New lead'), '1' => __('Pre-screened'), '2' => __('DQed'),'3' => __('Repeated submission'),'4' => __('Tried contact and waiting')];
    }


    public function getMaritalStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['marital_status']) ? $data['marital_status'] : '');
        $list = $this->getMaritalStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getBirthdayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['birthday_time']) ? $data['birthday_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setBirthdayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

//    public function getCreatetimeAttr($value)
//    {
//        $value = $value ? $value : '';
//        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
//    }

//    public function getMobileAttr($value)
//    {
//        $value = $value ? $value : '';
//        if ($value){
//            $qian = substr($value, 0, 2);
//            if ($qian=='+1'){
//                $phone = substr($value,2);
//                return $value;
//
////                $html = '<span style="padding-right:5px">+1</span><span>'.$phone.'</span>';
////                $html .= '<a href="'.$value.'">
////                         <button type="button" class="btn btn-success cloudcall" data-type="clues" data-field="mobile" data-typeid="173">
////                         <i class="fa fa-phone"></i> Call
////                         </button>
////                         </a>';
////                return $html;
//            }elseif(strpos($value, '(')){
//                return $value;
//            }elseif(strpos($value, '-')){
//                return $value;
//            }else{
//                return $value;
//            }
//        }else{
//            return $value;
//        }
//
//    }

    function format_phone($phone)
    {
        $phone = preg_replace("/[^0-9]/", "", $phone);
        if(strlen($phone) == 7)
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif(strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","($1) $2-$3",$phone);
        else
            return $phone;
    }
}
