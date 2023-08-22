<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class DonorEducation extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'donor_education';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_university_text',
        'is_plan_education_text',
        'status_text'
    ];
    

    
    public function getIsUniversityList()
    {
        return ['1' => __('Is_university 1'), '2' => __('Is_university 2')];
    }

    public function getIsPlanEducationList()
    {
        return ['1' => __('Is_plan_education 1'), '2' => __('Is_plan_education 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsUniversityTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_university']) ? $data['is_university'] : '');
        $list = $this->getIsUniversityList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsPlanEducationTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_plan_education']) ? $data['is_plan_education'] : '');
        $list = $this->getIsPlanEducationList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
