<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class SurrogateBaby extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'surrogate_baby';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'birthday_time_text',
        'sex_text',
        'pregnancy_type_text',
        'is_surrogate_text'
    ];
    

    
    public function getSexList()
    {
        return ['0' => __('Sex 0'), '1' => __('Sex 1')];
    }

    public function getPregnancyTypeList()
    {
        return ['0' => __('Pregnancy_type 0'), '1' => __('Pregnancy_type 1')];
    }

    public function getIsSurrogateList()
    {
        return ['1' => __('Is_surrogate 1'), '2' => __('Is_surrogate 2')];
    }


    public function getBirthdayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['birthday_time']) ? $data['birthday_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getSexTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sex']) ? $data['sex'] : '');
        $list = $this->getSexList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPregnancyTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pregnancy_type']) ? $data['pregnancy_type'] : '');
        $list = $this->getPregnancyTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSurrogateTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_surrogate']) ? $data['is_surrogate'] : '');
        $list = $this->getIsSurrogateList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setBirthdayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
