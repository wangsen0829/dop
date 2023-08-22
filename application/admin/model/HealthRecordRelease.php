<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class HealthRecordRelease extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'health_record_release';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_one_text',
        'is_two_text',
        'is_three_text',
        'is_four_text',
        'is_five_text',
        'canvas_time_text',
        'name_time_text',
        'status_text'
    ];
    

    
    public function getIsOneList()
    {
        return ['1' => __('Is_one 1'), '2' => __('Is_one 2')];
    }

    public function getIsTwoList()
    {
        return ['1' => __('Is_two 1'), '2' => __('Is_two 2')];
    }

    public function getIsThreeList()
    {
        return ['1' => __('Is_three 1'), '2' => __('Is_three 2')];
    }

    public function getIsFourList()
    {
        return ['1' => __('Is_four 1'), '2' => __('Is_four 2')];
    }

    public function getIsFiveList()
    {
        return ['1' => __('Is_five 1'), '2' => __('Is_five 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsOneTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_one']) ? $data['is_one'] : '');
        $list = $this->getIsOneList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTwoTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_two']) ? $data['is_two'] : '');
        $list = $this->getIsTwoList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsThreeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_three']) ? $data['is_three'] : '');
        $list = $this->getIsThreeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsFourTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_four']) ? $data['is_four'] : '');
        $list = $this->getIsFourList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsFiveTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_five']) ? $data['is_five'] : '');
        $list = $this->getIsFiveList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCanvasTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['canvas_time']) ? $data['canvas_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getNameTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['name_time']) ? $data['name_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setCanvasTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setNameTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
