<?php

namespace app\admin\model;

use think\Model;


class MedicalInformation extends Model
{

    

    

    // 表名
    protected $table = 'medical_information';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'covid_text',
        'obgyn_visit_time_text',
        'pap_smear_time_text',
        'menstruation_start_time_text',
        'menstruation_long_time_text',
        'is_menstruation_regular_text',
        'is_any_infectious_disease_text',
        'is_any_genetic_disease_text',
        'is_mental_illness_text',
        'is_other_disease_text',
        'is_take_any_medicine_text',
        'is_allergic_medication_text',
        'is_weightc_change_text',
        'is_any_surgeries_text',
        'is_twins_text',
        'is_children_live_text',
        'is_having_more_children_text',
        'any_diseases_text',
        'status_text'
    ];
    

    
    public function getCovidList()
    {
        return ['0' => __('Covid 0'), '1' => __('Covid 1'), '2' => __('Covid 2'), '3' => __('Covid 3'), '4' => __('Covid 4')];
    }

    public function getIsMenstruationRegularList()
    {
        return ['1' => __('Is_menstruation_regular 1'), '2' => __('Is_menstruation_regular 2')];
    }

    public function getIsAnyInfectiousDiseaseList()
    {
        return ['1' => __('Is_any_infectious_disease 1'), '2' => __('Is_any_infectious_disease 2')];
    }

    public function getIsAnyGeneticDiseaseList()
    {
        return ['1' => __('Is_any_genetic_disease 1'), '2' => __('Is_any_genetic_disease 2')];
    }

    public function getIsMentalIllnessList()
    {
        return ['1' => __('Is_mental_illness 1'), '2' => __('Is_mental_illness 2')];
    }

    public function getIsOtherDiseaseList()
    {
        return ['1' => __('Is_other_disease 1'), '2' => __('Is_other_disease 2')];
    }

    public function getIsTakeAnyMedicineList()
    {
        return ['1' => __('Is_take_any_medicine 1'), '2' => __('Is_take_any_medicine 2')];
    }

    public function getIsAllergicMedicationList()
    {
        return ['1' => __('Is_allergic_medication 1'), '2' => __('Is_allergic_medication 2')];
    }

    public function getIsWeightcChangeList()
    {
        return ['1' => __('Is_weightc_change 1'), '2' => __('Is_weightc_change 2')];
    }

    public function getIsAnySurgeriesList()
    {
        return ['1' => __('Is_any_surgeries 1'), '2' => __('Is_any_surgeries 2')];
    }

    public function getIsTwinsList()
    {
        return ['1' => __('Is_twins 1'), '2' => __('Is_twins 2')];
    }

    public function getIsChildrenLiveList()
    {
        return ['1' => __('Is_children_live 1'), '2' => __('Is_children_live 2')];
    }

    public function getIsHavingMoreChildrenList()
    {
        return ['1' => __('Is_having_more_children 1'), '2' => __('Is_having_more_children 2')];
    }

    public function getAnyDiseasesList()
    {
        return ['0' => __('Any_diseases 0'), '1' => __('Any_diseases 1'), '2' => __('Any_diseases 2'), '3' => __('Any_diseases 3'), '4' => __('Any_diseases 4'), '5' => __('Any_diseases 5'), '6' => __('Any_diseases 6'), '7' => __('Any_diseases 7'), '8' => __('Any_diseases 8')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getCovidTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['covid']) ? $data['covid'] : '');
        $list = $this->getCovidList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getObgynVisitTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['obgyn_visit_time']) ? $data['obgyn_visit_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getPapSmearTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pap_smear_time']) ? $data['pap_smear_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getMenstruationStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['menstruation_start_time']) ? $data['menstruation_start_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getMenstruationLongTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['menstruation_long_time']) ? $data['menstruation_long_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getIsMenstruationRegularTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_menstruation_regular']) ? $data['is_menstruation_regular'] : '');
        $list = $this->getIsMenstruationRegularList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAnyInfectiousDiseaseTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_any_infectious_disease']) ? $data['is_any_infectious_disease'] : '');
        $list = $this->getIsAnyInfectiousDiseaseList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAnyGeneticDiseaseTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_any_genetic_disease']) ? $data['is_any_genetic_disease'] : '');
        $list = $this->getIsAnyGeneticDiseaseList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMentalIllnessTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_mental_illness']) ? $data['is_mental_illness'] : '');
        $list = $this->getIsMentalIllnessList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsOtherDiseaseTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_other_disease']) ? $data['is_other_disease'] : '');
        $list = $this->getIsOtherDiseaseList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTakeAnyMedicineTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_take_any_medicine']) ? $data['is_take_any_medicine'] : '');
        $list = $this->getIsTakeAnyMedicineList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAllergicMedicationTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_allergic_medication']) ? $data['is_allergic_medication'] : '');
        $list = $this->getIsAllergicMedicationList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsWeightcChangeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_weightc_change']) ? $data['is_weightc_change'] : '');
        $list = $this->getIsWeightcChangeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAnySurgeriesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_any_surgeries']) ? $data['is_any_surgeries'] : '');
        $list = $this->getIsAnySurgeriesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTwinsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_twins']) ? $data['is_twins'] : '');
        $list = $this->getIsTwinsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsChildrenLiveTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_children_live']) ? $data['is_children_live'] : '');
        $list = $this->getIsChildrenLiveList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsHavingMoreChildrenTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_having_more_children']) ? $data['is_having_more_children'] : '');
        $list = $this->getIsHavingMoreChildrenList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAnyDiseasesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['any_diseases']) ? $data['any_diseases'] : '');
        $list = $this->getAnyDiseasesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setObgynVisitTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPapSmearTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setMenstruationStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setMenstruationLongTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
