<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class MedicalInformation extends Backend
{

    /**
     * MedicalInformation模型对象
     * @var \app\admin\model\MedicalInformation
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\MedicalInformation;
        $this->view->assign("covidList", $this->model->getCovidList());
        $this->view->assign("isMenstruationRegularList", $this->model->getIsMenstruationRegularList());
        $this->view->assign("isAnyInfectiousDiseaseList", $this->model->getIsAnyInfectiousDiseaseList());
        $this->view->assign("isAnyGeneticDiseaseList", $this->model->getIsAnyGeneticDiseaseList());
        $this->view->assign("isMentalIllnessList", $this->model->getIsMentalIllnessList());
        $this->view->assign("isOtherDiseaseList", $this->model->getIsOtherDiseaseList());
        $this->view->assign("isTakeAnyMedicineList", $this->model->getIsTakeAnyMedicineList());
        $this->view->assign("isAllergicMedicationList", $this->model->getIsAllergicMedicationList());
        $this->view->assign("isWeightcChangeList", $this->model->getIsWeightcChangeList());
        $this->view->assign("isAnySurgeriesList", $this->model->getIsAnySurgeriesList());
        $this->view->assign("isTwinsList", $this->model->getIsTwinsList());
        $this->view->assign("isChildrenLiveList", $this->model->getIsChildrenLiveList());
        $this->view->assign("isHavingMoreChildrenList", $this->model->getIsHavingMoreChildrenList());
        $this->view->assign("anyDiseasesList", $this->model->getAnyDiseasesList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
