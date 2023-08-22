<?php

namespace app\admin\model;

use app\common\model\MoneyLog;
use app\common\model\ScoreLog;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    use SoftDelete;
    // 表名
    protected $name = 'user';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';
    // 追加属性
    protected $append = [
        'prevtime_text',
        'logintime_text',
        'jointime_text'
    ];
    public function getOriginData()
    {
        return $this->origin;
    }

    protected static function init()
    {
        self::beforeUpdate(function ($row) {
            $changed = $row->getChangedData();
            //如果有修改密码
            if (isset($changed['password'])) {
                if ($changed['password']) {
                    $salt = \fast\Random::alnum();
                    $row->password = \app\common\library\Auth::instance()->getEncryptPassword($changed['password'], $salt);
                    $row->salt = $salt;
                } else {
                    unset($row->password);
                }
            }
        });


        self::beforeUpdate(function ($row) {
            $changedata = $row->getChangedData();
            $origin = $row->getOriginData();
            if (isset($changedata['money']) && (function_exists('bccomp') ? bccomp($changedata['money'], $origin['money'], 2) !== 0 : (double)$changedata['money'] !== (double)$origin['money'])) {
                MoneyLog::create(['user_id' => $row['id'], 'money' => $changedata['money'] - $origin['money'], 'before' => $origin['money'], 'after' => $changedata['money'], 'memo' => '管理员变更金额']);
            }
            if (isset($changedata['score']) && (int)$changedata['score'] !== (int)$origin['score']) {
                ScoreLog::create(['user_id' => $row['id'], 'score' => $changedata['score'] - $origin['score'], 'before' => $origin['score'], 'after' => $changedata['score'], 'memo' => '管理员变更积分']);
            }
        });
    }

    public function getGenderList()
    {
        return ['1' => __('Male'), '0' => __('Female')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getPrevtimeTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['prevtime'] ?? "");
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getLogintimeTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['logintime'] ?? "");
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }

    public function getJointimeTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['jointime'] ?? "");
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }

    protected function setPrevtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setLogintimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setJointimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setBirthdayAttr($value)
    {
        return $value ? $value : null;
    }

    public function group()
    {
        return $this->belongsTo('UserGroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function prescreen()
    {
        return $this->hasOne('PreScreen', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function personal()
    {
        return $this->hasOne('PersonalInfo', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function ob()
    {
        return $this->hasOne('ObstetricHistory', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function medical()
    {
        return $this->hasOne('MedicalInformation', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function about()
    {
        return $this->hasOne('AboutSurrogacy', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function other()
    {
        return $this->hasOne('OtherInformation', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function photo()
    {
        return $this->hasOne('SurrogatePhoto', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function health()
    {
        return $this->hasOne('HealthRecordRelease', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function background()
    {
        return $this->hasOne('Background', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function sbp()
    {
        return $this->hasOne('SurrogacySbp', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function examine()
    {
        return $this->hasOne('Examine', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function donorPrescreen()
    {
        return $this->hasOne('DonorPreScreen', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function donorPhotos()
    {
        return $this->hasOne('DonorPhotos', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function donorPersonal()
    {
        return $this->hasOne('DonorPersonal', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function education()
    {
        return $this->hasOne('DonorEducation', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function character()
    {
        return $this->hasOne('DonorCharacter', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function donorMedical()
    {
        return $this->hasOne('DonorMedical', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function donorExamine()
    {
        return $this->hasOne('DonorExamine', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function getLogintimeAttr($value)
    {
        $value = $value ? $value : '';
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }
    public function getCreatetimeAttr($value)
    {
        $value = $value ? $value : '';
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }
    public function getPassTimeAttr($value)
    {
        $value = $value ? $value : '';
        return is_numeric($value) ? date("m-d-Y H:i:s", $value) : $value;
    }
}
