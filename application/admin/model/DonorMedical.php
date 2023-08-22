<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class DonorMedical extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'donor_medical';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_hospital_text',
        'is_surgeries_text',
        'is_drink_text',
        'is_smoke_text',
        'is_menstrual_cycles_text',
        'is_pregnant_text',
        'status_text'
    ];
    

    
    public function getIsHospitalList()
    {
        return ['1' => __('Is_hospital 1'), '2' => __('Is_hospital 2')];
    }

    public function getIsSurgeriesList()
    {
        return ['1' => __('Is_surgeries 1'), '2' => __('Is_surgeries 2')];
    }

    public function getIsDrinkList()
    {
        return ['1' => __('Is_drink 1'), '2' => __('Is_drink 2')];
    }

    public function getIsSmokeList()
    {
        return ['1' => __('Is_smoke 1'), '2' => __('Is_smoke 2')];
    }

    public function getIsMenstrualCyclesList()
    {
        return ['1' => __('Is_menstrual_cycles 1'), '2' => __('Is_menstrual_cycles 2')];
    }

    public function getIsPregnantList()
    {
        return ['1' => __('Is_pregnant 1'), '2' => __('Is_pregnant 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsHospitalTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_hospital']) ? $data['is_hospital'] : '');
        $list = $this->getIsHospitalList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSurgeriesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_surgeries']) ? $data['is_surgeries'] : '');
        $list = $this->getIsSurgeriesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDrinkTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_drink']) ? $data['is_drink'] : '');
        $list = $this->getIsDrinkList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSmokeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_smoke']) ? $data['is_smoke'] : '');
        $list = $this->getIsSmokeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMenstrualCyclesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_menstrual_cycles']) ? $data['is_menstrual_cycles'] : '');
        $list = $this->getIsMenstrualCyclesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsPregnantTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_pregnant']) ? $data['is_pregnant'] : '');
        $list = $this->getIsPregnantList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
