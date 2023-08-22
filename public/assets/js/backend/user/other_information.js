define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/other_information/index' + location.search,
                    add_url: 'user/other_information/add',
                    edit_url: 'user/other_information/edit',
                    del_url: 'user/other_information/del',
                    multi_url: 'user/other_information/multi',
                    import_url: 'user/other_information/import',
                    table: 'other_information',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'uid', title: __('Uid')},
                        {field: 'is_smoke', title: __('Is_smoke'), searchList: {"1":__('Is_smoke 1'),"2":__('Is_smoke 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_family_smoke', title: __('Is_family_smoke'), searchList: {"1":__('Is_family_smoke 1'),"2":__('Is_family_smoke 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_criminal_records', title: __('Is_criminal_records'), searchList: {"1":__('Is_criminal_records 1'),"2":__('Is_criminal_records 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_crga', title: __('Is_crga'), searchList: {"1":__('Is_crga 1'),"2":__('Is_crga 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_tattoos', title: __('Is_tattoos'), searchList: {"1":__('Is_tattoos 1'),"2":__('Is_tattoos 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_bankruptcy', title: __('Is_bankruptcy'), searchList: {"1":__('Is_bankruptcy 1'),"2":__('Is_bankruptcy 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_blood', title: __('Is_blood'), searchList: {"1":__('Is_blood 1'),"2":__('Is_blood 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_bb', title: __('Is_bb'), searchList: {"1":__('Is_bb 1'),"2":__('Is_bb 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_pm', title: __('Is_pm'), searchList: {"1":__('Is_pm 1'),"2":__('Is_pm 2')}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'user/other_information/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '140px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'user/other_information/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/other_information/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
