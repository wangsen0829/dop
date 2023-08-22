<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class NotesSurrogacy extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'notes_surrogacy';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text',
        'contact_method_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3')];
    }

    public function getContactMethodList()
    {
        return ['1' => __('Contact_method 1'), '2' => __('Contact_method 2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getContactMethodTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['contact_method']) ? $data['contact_method'] : '');
        $list = $this->getContactMethodList();
        return isset($list[$value]) ? $list[$value] : '';
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
        return $this->belongsTo('User', 'form_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
