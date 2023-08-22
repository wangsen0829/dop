<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class DonorLead extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'donor_lead';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_injections_text',
        'is_attend_appointment_text',
        'is_any_medications_text',
        'is_donated_text',
        'is_egg_donor_text',
        'is_plan_text',
        'is_smoke_text',
        'is_drink_text',
        'is_menstrual_regular_text',
        'is_illicit_drugs_text',
        'crearetime_text'
    ];
    

    
    public function getIsInjectionsList()
    {
        return ['1' => __('Is_injections 1'), '2' => __('Is_injections 2')];
    }

    public function getIsAttendAppointmentList()
    {
        return ['1' => __('Is_attend_appointment 1'), '2' => __('Is_attend_appointment 2')];
    }

    public function getIsAnyMedicationsList()
    {
        return ['1' => __('Is_any_medications 1'), '2' => __('Is_any_medications 2')];
    }

    public function getIsDonatedList()
    {
        return ['1' => __('Is_donated 1'), '2' => __('Is_donated 2')];
    }

    public function getIsEggDonorList()
    {
        return ['1' => __('Is_egg_donor 1'), '2' => __('Is_egg_donor 2')];
    }

    public function getIsPlanList()
    {
        return ['1' => __('Is_plan 1'), '2' => __('Is_plan 2')];
    }

    public function getIsSmokeList()
    {
        return ['1' => __('Is_smoke 1'), '2' => __('Is_smoke 2')];
    }

    public function getIsDrinkList()
    {
        return ['1' => __('Is_drink 1'), '2' => __('Is_drink 2')];
    }

    public function getIsMenstrualRegularList()
    {
        return ['1' => __('Is_menstrual_regular 1'), '2' => __('Is_menstrual_regular 2')];
    }

    public function getIsIllicitDrugsList()
    {
        return ['1' => __('Is_illicit_drugs 1'), '2' => __('Is_illicit_drugs 2')];
    }


    public function getIsInjectionsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_injections']) ? $data['is_injections'] : '');
        $list = $this->getIsInjectionsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAttendAppointmentTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_attend_appointment']) ? $data['is_attend_appointment'] : '');
        $list = $this->getIsAttendAppointmentList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAnyMedicationsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_any_medications']) ? $data['is_any_medications'] : '');
        $list = $this->getIsAnyMedicationsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDonatedTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_donated']) ? $data['is_donated'] : '');
        $list = $this->getIsDonatedList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsEggDonorTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_egg_donor']) ? $data['is_egg_donor'] : '');
        $list = $this->getIsEggDonorList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsPlanTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_plan']) ? $data['is_plan'] : '');
        $list = $this->getIsPlanList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSmokeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_smoke']) ? $data['is_smoke'] : '');
        $list = $this->getIsSmokeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDrinkTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_drink']) ? $data['is_drink'] : '');
        $list = $this->getIsDrinkList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMenstrualRegularTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_menstrual_regular']) ? $data['is_menstrual_regular'] : '');
        $list = $this->getIsMenstrualRegularList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsIllicitDrugsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_illicit_drugs']) ? $data['is_illicit_drugs'] : '');
        $list = $this->getIsIllicitDrugsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCrearetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['crearetime']) ? $data['crearetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCrearetimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
