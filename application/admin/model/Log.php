<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Log extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];


    public function getCreatetimeAttr($value)
    {
        $value = $value ? $value : '';
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }







}
