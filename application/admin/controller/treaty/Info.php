<?php

namespace app\admin\controller\treaty;

use addons\treaty\common\library\TreatyWord;
use app\admin\model\treaty\Treaty as TreatyModel;
use app\common\controller\Backend;
use app\admin\model\treaty\Category;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * 协议管理
 *
 * @icon fa fa-circle-o
 */
class Info extends Backend
{

    /**
     * Treaty模型对象
     * @var \app\admin\model\treaty\Treaty
     */
    protected $model = null;

    protected $noNeedRight = ["clear"];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\treaty\Treaty;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['treatycategory','user'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['treatycategory','user'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as &$row) {
                $row->getRelation('treatycategory')->visible(['name']);
                $row->getRelation('user')->visible(['nickname']);
            }
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    public function export_pdf(){
        $id = $this->request->get("ids");
        $treaty_info =TreatyModel::where(["id"=>$id])->find();
        $treaty_category = Category::where(["id"=>$treaty_info["category_id"]])->find();
        TreatyModel::exportPdf($treaty_info, $treaty_category);
    }


    public function clear(){
        $file_path = './uploads' . DS . 'treaty';
        rmdirs($file_path,false);
        $this->success("清理成功");

    }


}

