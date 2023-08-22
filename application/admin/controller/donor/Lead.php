<?php

namespace app\admin\controller\donor;

use app\admin\model\DonorCharacter;
use app\admin\model\DonorEducation;
use app\admin\model\DonorEthnicity;
use app\admin\model\DonorPersonal;
use app\admin\model\DonorPreScreen;
use app\admin\model\FormNumber;
use app\common\controller\Backend;
use think\Request;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Lead extends Backend
{

    /**
     * DonorLead模型对象
     * @var \app\admin\model\DonorLead
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\DonorLead;
        $this->view->assign("isInjectionsList", $this->model->getIsInjectionsList());
        $this->view->assign("isAttendAppointmentList", $this->model->getIsAttendAppointmentList());
        $this->view->assign("isAnyMedicationsList", $this->model->getIsAnyMedicationsList());
        $this->view->assign("isDonatedList", $this->model->getIsDonatedList());
        $this->view->assign("isEggDonorList", $this->model->getIsEggDonorList());
        $this->view->assign("isPlanList", $this->model->getIsPlanList());
        $this->view->assign("isSmokeList", $this->model->getIsSmokeList());
        $this->view->assign("isDrinkList", $this->model->getIsDrinkList());
        $this->view->assign("isMenstrualRegularList", $this->model->getIsMenstrualRegularList());
        $this->view->assign("isIllicitDrugsList", $this->model->getIsIllicitDrugsList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */



}
