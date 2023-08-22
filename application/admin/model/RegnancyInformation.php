<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class RegnancyInformation extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'regnancy_information';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'protect_time_text'
    ];
    

    



    public function getProtectTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['protect_time']) ? $data['protect_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setProtectTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
