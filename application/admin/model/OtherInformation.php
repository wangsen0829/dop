<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class OtherInformation extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'other_information';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_smoke_text',
        'is_family_smoke_text',
        'is_criminal_records_text',
        'is_crga_text',
        'is_tattoos_text',
        'is_bankruptcy_text',
        'is_blood_text',
        'is_bb_text',
        'is_pm_text'
    ];
    

    
    public function getIsSmokeList()
    {
        return ['1' => __('Is_smoke 1'), '2' => __('Is_smoke 2')];
    }

    public function getIsFamilySmokeList()
    {
        return ['1' => __('Is_family_smoke 1'), '2' => __('Is_family_smoke 2')];
    }

    public function getIsCriminalRecordsList()
    {
        return ['1' => __('Is_criminal_records 1'), '2' => __('Is_criminal_records 2')];
    }

    public function getIsCrgaList()
    {
        return ['1' => __('Is_crga 1'), '2' => __('Is_crga 2')];
    }

    public function getIsTattoosList()
    {
        return ['1' => __('Is_tattoos 1'), '2' => __('Is_tattoos 2')];
    }

    public function getIsBankruptcyList()
    {
        return ['1' => __('Is_bankruptcy 1'), '2' => __('Is_bankruptcy 2')];
    }

    public function getIsBloodList()
    {
        return ['1' => __('Is_blood 1'), '2' => __('Is_blood 2')];
    }

    public function getIsBbList()
    {
        return ['1' => __('Is_bb 1'), '2' => __('Is_bb 2')];
    }

    public function getIsPmList()
    {
        return ['1' => __('Is_pm 1'), '2' => __('Is_pm 2')];
    }


    public function getIsSmokeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_smoke']) ? $data['is_smoke'] : '');
        $list = $this->getIsSmokeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsFamilySmokeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_family_smoke']) ? $data['is_family_smoke'] : '');
        $list = $this->getIsFamilySmokeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsCriminalRecordsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_criminal_records']) ? $data['is_criminal_records'] : '');
        $list = $this->getIsCriminalRecordsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsCrgaTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_crga']) ? $data['is_crga'] : '');
        $list = $this->getIsCrgaList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTattoosTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_tattoos']) ? $data['is_tattoos'] : '');
        $list = $this->getIsTattoosList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsBankruptcyTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_bankruptcy']) ? $data['is_bankruptcy'] : '');
        $list = $this->getIsBankruptcyList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsBloodTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_blood']) ? $data['is_blood'] : '');
        $list = $this->getIsBloodList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsBbTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_bb']) ? $data['is_bb'] : '');
        $list = $this->getIsBbList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsPmTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_pm']) ? $data['is_pm'] : '');
        $list = $this->getIsPmList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
