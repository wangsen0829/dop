<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Notes extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'notes';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text',
        'status_text',
        'nexttime_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getNexttimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['nexttime']) ? $data['nexttime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setNexttimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    public function getCreatetimeAttr($value)
    {
        $value = $value ? $value : '';
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }
    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function form()
    {
        return $this->belongsTo('PreScreenForm', 'form_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
