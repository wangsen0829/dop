<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class PreScreen extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'pre_screen';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'marital_status_text',
        'is_pregnant_text',
        'status_text'
    ];
    

    
    public function getMaritalStatusList()
    {
        return ['not married' => __('Not married'), 'married' => __('Married'), 'divorce' => __('Divorce')];
    }

    public function getIsPregnantList()
    {
        return ['0' => __('Is_pregnant 0'), '1' => __('Is_pregnant 1')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getMaritalStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['marital_status']) ? $data['marital_status'] : '');
        $list = $this->getMaritalStatusList();
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
