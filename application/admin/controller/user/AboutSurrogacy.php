<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class AboutSurrogacy extends Backend
{

    /**
     * AboutSurrogacy模型对象
     * @var \app\admin\model\AboutSurrogacy
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\AboutSurrogacy;
        $this->view->assign("isSurrogateList", $this->model->getIsSurrogateList());
        $this->view->assign("isConceiveTwinsList", $this->model->getIsConceiveTwinsList());
        $this->view->assign("isFetalReductionList", $this->model->getIsFetalReductionList());
        $this->view->assign("isInducedAbortionList", $this->model->getIsInducedAbortionList());
        $this->view->assign("isCvsList", $this->model->getIsCvsList());
        $this->view->assign("isTomList", $this->model->getIsTomList());
        $this->view->assign("isMeetParentsList", $this->model->getIsMeetParentsList());
        $this->view->assign("isRelinquishList", $this->model->getIsRelinquishList());
        $this->view->assign("isSsList", $this->model->getIsSsList());
        $this->view->assign("isBreastFeedList", $this->model->getIsBreastFeedList());
        $this->view->assign("isSexualLifeList", $this->model->getIsSexualLifeList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
