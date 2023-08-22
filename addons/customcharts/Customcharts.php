<?php

namespace addons\customcharts;

use app\common\library\Menu;
use think\Addons;
use think\Db;
use app\common\library\Auth;
use think\Config;
use think\Loader;
use think\Request;

/**
 * 插件
 */
class Customcharts extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'customcharts',
                'title'   => 'DIY图表统计工具',
                'icon'    => 'fa fa-bar-chart',
                'sublist' => [
                    [
                        'name'    => 'customcharts/totalnumber',
                        'title'   => '数量统计管理',
                        'icon'    => 'fa fa-circle-o',
                        'sublist' => [
                            ['name' => 'customcharts/totalnumber/index', 'title' => '查看'],
                            ['name' => 'customcharts/totalnumber/add',   'title' => '添加'],
                            ['name' => 'customcharts/totalnumber/edit',  'title' => '编辑'],
                            ['name' => 'customcharts/totalnumber/del',   'title' => '删除'],
                        ]
                    ],
                    [
                        'name'    => 'customcharts/chart',
                        'title'   => '图表统计管理',
                        'icon'    => 'fa fa-circle-o',
                        'remark'  => '图表数据合并(类型、标题、描述一样即可合并到一个统计图上)，例如：创建两个曲线图统计，分别统计订单金额、订单数量，此时只需要将这两项统计的标题和描述值设为一样即可实现订单金额和订单数量在同一个图上显示。',
                        'sublist' => [
                            ['name' => 'customcharts/chart/index', 'title' => '查看'],
                            ['name' => 'customcharts/chart/add',   'title' => '添加'],
                            ['name' => 'customcharts/chart/edit',  'title' => '编辑'],
                            ['name' => 'customcharts/chart/del',   'title' => '删除'],
                        ]
                    ],
                    [
                        'name'    => 'customcharts/ranking',
                        'title'   => '排行统计管理',
                        'icon'    => 'fa fa-circle-o',
                        'sublist' => [
                            ['name' => 'customcharts/ranking/index', 'title' => '查看'],
                            ['name' => 'customcharts/ranking/add',   'title' => '添加'],
                            ['name' => 'customcharts/ranking/edit',  'title' => '编辑'],
                            ['name' => 'customcharts/ranking/del',   'title' => '删除'],
                        ]
                    ],
                    [
                        'name'    => 'customcharts/show',
                        'title'   => '统计控制台',
                        'icon'    => 'fa fa-circle-o',
                        'sublist' => [
                            ['name' => 'customcharts/show/index', 'title' => '查看'],
                        ]
                    ]
                ]
            ]
        ];

        Menu::create($menu);
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('customcharts');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('customcharts');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('customcharts');
    }
}
