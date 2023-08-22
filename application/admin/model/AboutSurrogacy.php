<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class AboutSurrogacy extends Model
{

    use SoftDelete;

    

    // 表名
    protected $table = 'about_surrogacy';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'is_surrogate_text',
        'is_conceive_twins_text',
        'is_fetal_reduction_text',
        'is_induced_abortion_text',
        'is_cvs_text',
        'is_tom_text',
        'is_meet_parents_text',
        'is_relinquish_text',
        'is_ss_text',
        'is_breast_feed_text',
        'is_sexual_life_text'
    ];
    

    
    public function getIsSurrogateList()
    {
        return ['1' => __('Is_surrogate 1'), '2' => __('Is_surrogate 2')];
    }

    public function getIsConceiveTwinsList()
    {
        return ['1' => __('Is_conceive_twins 1'), '2' => __('Is_conceive_twins 2')];
    }

    public function getIsFetalReductionList()
    {
        return ['1' => __('Is_fetal_reduction 1'), '2' => __('Is_fetal_reduction 2')];
    }

    public function getIsInducedAbortionList()
    {
        return ['1' => __('Is_induced_abortion 1'), '2' => __('Is_induced_abortion 2')];
    }

    public function getIsCvsList()
    {
        return ['1' => __('Is_cvs 1'), '2' => __('Is_cvs 2')];
    }

    public function getIsTomList()
    {
        return ['1' => __('Is_tom 1'), '2' => __('Is_tom 2')];
    }

    public function getIsMeetParentsList()
    {
        return ['1' => __('Is_meet_parents 1'), '2' => __('Is_meet_parents 2')];
    }

    public function getIsRelinquishList()
    {
        return ['1' => __('Is_relinquish 1'), '2' => __('Is_relinquish 2')];
    }

    public function getIsSsList()
    {
        return ['1' => __('Is_ss 1'), '2' => __('Is_ss 2')];
    }

    public function getIsBreastFeedList()
    {
        return ['1' => __('Is_breast_feed 1'), '2' => __('Is_breast_feed 2')];
    }

    public function getIsSexualLifeList()
    {
        return ['1' => __('Is_sexual_life 1'), '2' => __('Is_sexual_life 2')];
    }


    public function getIsSurrogateTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_surrogate']) ? $data['is_surrogate'] : '');
        $list = $this->getIsSurrogateList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsConceiveTwinsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_conceive_twins']) ? $data['is_conceive_twins'] : '');
        $list = $this->getIsConceiveTwinsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsFetalReductionTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_fetal_reduction']) ? $data['is_fetal_reduction'] : '');
        $list = $this->getIsFetalReductionList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsInducedAbortionTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_induced_abortion']) ? $data['is_induced_abortion'] : '');
        $list = $this->getIsInducedAbortionList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsCvsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_cvs']) ? $data['is_cvs'] : '');
        $list = $this->getIsCvsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTomTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_tom']) ? $data['is_tom'] : '');
        $list = $this->getIsTomList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMeetParentsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_meet_parents']) ? $data['is_meet_parents'] : '');
        $list = $this->getIsMeetParentsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsRelinquishTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_relinquish']) ? $data['is_relinquish'] : '');
        $list = $this->getIsRelinquishList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSsTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_ss']) ? $data['is_ss'] : '');
        $list = $this->getIsSsList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsBreastFeedTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_breast_feed']) ? $data['is_breast_feed'] : '');
        $list = $this->getIsBreastFeedList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSexualLifeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_sexual_life']) ? $data['is_sexual_life'] : '');
        $list = $this->getIsSexualLifeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
