<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class PersonalInfo extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'personal_info';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'blood_type_text',
        'race_text',
        'sexual_orientation_text',
        'family_attitude_text',
        'status_text'
    ];
    

    
    public function getBloodTypeList()
    {
        return ['0' => __('Blood_type 0'), '1' => __('Blood_type 1'), '2' => __('Blood_type 2'), '3' => __('Blood_type 3')];
    }

    public function getRaceList()
    {
        return ['0' => __('Race 0'), '1' => __('Race 1'), '2' => __('Race 2'), '3' => __('Race 3')];
    }

    public function getSexualOrientationList()
    {
        return ['0' => __('Sexual_orientation 0'), '1' => __('Sexual_orientation 1')];
    }

    public function getFamilyAttitudeList()
    {
        return ['yes' => __('Yes'), 'no' => __('No')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getBloodTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['blood_type']) ? $data['blood_type'] : '');
        $list = $this->getBloodTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getRaceTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['race']) ? $data['race'] : '');
        $list = $this->getRaceList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSexualOrientationTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sexual_orientation']) ? $data['sexual_orientation'] : '');
        $list = $this->getSexualOrientationList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFamilyAttitudeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['family_attitude']) ? $data['family_attitude'] : '');
        $list = $this->getFamilyAttitudeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
