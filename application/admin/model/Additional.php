<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Additional extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'additional_information';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_health_care_text',
        'status_text'
    ];
    

    
    public function getIsHealthCareList()
    {
        return ['1' => __('Is_health_care 1'), '2' => __('Is_health_care 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsHealthCareTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_health_care']) ? $data['is_health_care'] : '');
        $list = $this->getIsHealthCareList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
