<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class ObstetricHistory extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'obstetric_history';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_premature_birth_text',
//        'is_36_week_text',
        'is_artificial_abortion_text',
        'is_spontaneous_abortion_text',
        'status_text'
    ];
    

    
    public function getIsPrematureBirthList()
    {
        return ['1' => __('Is_premature_birth 1'), '2' => __('Is_premature_birth 2')];
    }

    public function getIs36WeekList()
    {
        return ['1' => __('Is_36_week 1'), '2' => __('Is_36_week 2')];
    }

    public function getIsArtificialAbortionList()
    {
        return ['1' => __('Is_artificial_abortion 1'), '2' => __('Is_artificial_abortion 2')];
    }

    public function getIsSpontaneousAbortionList()
    {
        return ['1' => __('Is_spontaneous_abortion 1'), '2' => __('Is_spontaneous_abortion 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsPrematureBirthTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_premature_birth']) ? $data['is_premature_birth'] : '');
        $list = $this->getIsPrematureBirthList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIs36WeekTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_36_week']) ? $data['is_36_week'] : '');
        $list = $this->getIs36WeekList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsArtificialAbortionTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_artificial_abortion']) ? $data['is_artificial_abortion'] : '');
        $list = $this->getIsArtificialAbortionList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSpontaneousAbortionTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_spontaneous_abortion']) ? $data['is_spontaneous_abortion'] : '');
        $list = $this->getIsSpontaneousAbortionList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
