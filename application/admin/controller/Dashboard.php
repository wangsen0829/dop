<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\Notes;
use app\admin\model\PreScreenForm;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\Attachment;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon   fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        
        $url = Config('url').'?aff='.$this->auth->id;
        $this->view->assign("url", $url);
        return $this->view->fetch();
    }

    public function test(){
        $time = strtotime(date('Y-m-d',time()))+(68400-1);
        $form_ids = Notes::where('contact_status',1)->where('nexttime','<',$time)
        ->group('form_id')
        ->order('createtime','desc')->column('form_id');
         $form_ids = implode(',',$form_ids);
          dump($form_ids);

        ;
//        return $this->view->fetch();
    }

}
