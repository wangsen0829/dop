<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class SurrogacyLead extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'surrogacy_lead';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'birthday_time_text',
        'marital_status_text'
    ];
    

    
    public function getMaritalStatusList()
    {
        return ['1' => __('Marital_status 1'), '2' => __('Marital_status 2')];
    }


    public function getBirthdayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['birthday_time']) ? $data['birthday_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getMaritalStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['marital_status']) ? $data['marital_status'] : '');
        $list = $this->getMaritalStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setBirthdayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
