<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class NewLead extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'new_lead';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'service_text',
        'status_text',
        'type_text',
        'is_repeat_text'
    ];
    

    
    public function getServiceList()
    {
        return ['1' => __('Service 1'), '2' => __('Service 2'), '3' => __('Service 3'), '4' => __('Service 4'), '5' => __('Service 5')];
    }

    public function getStatusList()
    {
        return ['0' => __('New lead'), '1' => __('Pre-screened'), '2' => __('DQed'),'3' => __('Tried contact and waiting')];
    }

    public function getTypeList()
    {
//        return ['1' => __('后台添加'), '2' => __('网站表单提交'), '3' => __('广告')];
        return ['1' => __('Home add'), '2' => __('Form Submission'), '3' => __('Advert;')];
    }

    public function getIsRepeatList()
    {
        return ['1' => __('Is_repeat 1'), '2' => __('Is_repeat 2')];
    }


    public function getServiceTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['service']) ? $data['service'] : '');
        $list = $this->getServiceList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsRepeatTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_repeat']) ? $data['is_repeat'] : '');
        $list = $this->getIsRepeatList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    function surrogacy(){
        return $this->hasOne('SurrogacyLead','form_id');
    }

    function donor(){
        return $this->hasOne('DonorLead','form_id');
    }

    function SpermDonation(){
        return $this->hasOne('SpermDonationLead','form_id');
    }

    function findSurrogacy(){
        return $this->hasOne('FindSurrogacyLead','form_id');
    }

    function other(){
        return $this->hasOne('OtherLead','form_id');
    }

    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function getCreatetimeAttr($value)
    {
        $value = $value ? $value : '';
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }

}
