<?php

namespace app\admin\controller;
use app\admin\model\Admin;
use app\admin\model\RegnancyInformation;
use app\admin\model\AboutSurrogacy;
use app\admin\model\Examine;
use app\admin\model\SurrogateBaby;
use app\admin\model\Background;
use app\admin\model\AdditionalInformation;
use app\admin\model\FormNumber;
use app\admin\model\HealthRecordRelease;
use app\admin\model\MedicalInformation;
use app\admin\model\ObstetricHistory;
use app\admin\model\OtherInformation;
use app\admin\model\PersonalInfo;
use app\admin\model\PreScreen;
use app\admin\model\SurrogatePhoto;
use app\common\controller\Backend;
use app\common\library\Auth;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Fenpei extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id,email,mobile,first_name,last_name';

    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
    }

    /**
     * 查看
     */
    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null);

            $where1['id'] = array('not in','1');

            $total = $this->model
                ->where($where)
                ->where($where1)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->where($where1)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $partent_ids = \app\admin\model\AuthGroupAccess::where('group_id',6)->column('uid');
        $parents = Admin::where('id','in',$partent_ids)->order('id','desc')->select();

        $this->view->assign('parents',$parents);
        return $this->view->fetch();
    }


}
