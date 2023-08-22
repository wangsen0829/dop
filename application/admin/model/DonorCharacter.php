<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class DonorCharacter extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'donor_character';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_outgoing_text',
        'is_athletic_text',
        'is_artistic_text',
        'play_instrument_text',
        'status_text'
    ];
    

    
    public function getIsOutgoingList()
    {
        return ['1' => __('Is_outgoing 1'), '2' => __('Is_outgoing 2')];
    }

    public function getIsAthleticList()
    {
        return ['1' => __('Is_athletic 1'), '2' => __('Is_athletic 2')];
    }

    public function getIsArtisticList()
    {
        return ['1' => __('Is_artistic 1'), '2' => __('Is_artistic 2')];
    }

    public function getPlayInstrumentList()
    {
        return ['1' => __('Play_instrument 1'), '2' => __('Play_instrument 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsOutgoingTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_outgoing']) ? $data['is_outgoing'] : '');
        $list = $this->getIsOutgoingList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAthleticTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_athletic']) ? $data['is_athletic'] : '');
        $list = $this->getIsAthleticList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsArtisticTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_artistic']) ? $data['is_artistic'] : '');
        $list = $this->getIsArtisticList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPlayInstrumentTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['play_instrument']) ? $data['play_instrument'] : '');
        $list = $this->getPlayInstrumentList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
