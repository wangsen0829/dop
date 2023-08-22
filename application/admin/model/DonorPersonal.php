<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class DonorPersonal extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'donor_personal';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_dimples_text',
        'is_eye_glasses_text',
        'is_had_braces_text',
        'is_tattoos_text',
        'is_piercings_text',
        'status_text'
    ];
    

    
    public function getIsDimplesList()
    {
        return ['1' => __('Is_dimples 1'), '2' => __('Is_dimples 2')];
    }

    public function getIsEyeGlassesList()
    {
        return ['1' => __('Is_eye_glasses 1'), '2' => __('Is_eye_glasses 2')];
    }

    public function getIsHadBracesList()
    {
        return ['1' => __('Is_had_braces 1'), '2' => __('Is_had_braces 2')];
    }

    public function getIsTattoosList()
    {
        return ['1' => __('Is_tattoos 1'), '2' => __('Is_tattoos 2')];
    }

    public function getIsPiercingsList()
    {
        return ['1' => __('Is_piercings 1'), '2' => __('Is_piercings 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getIsDimplesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_dimples']) ? $data['is_dimples'] : '');
        $list = $this->getIsDimplesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsEyeGlassesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_eye_glasses']) ? $data['is_eye_glasses'] : '');
        $list = $this->getIsEyeGlassesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsHadBracesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_had_braces']) ? $data['is_had_braces'] : '');
        $list = $this->getIsHadBracesList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTattoosTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_tattoos']) ? $data['is_tattoos'] : '');
        $list = $this->getIsTattoosList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsPiercingsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_piercings']) ? $data['is_piercings'] : '');
        $list = $this->getIsPiercingsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
