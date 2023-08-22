define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/health_record_release/index' + location.search,
                    add_url: 'user/health_record_release/add',
                    edit_url: 'user/health_record_release/edit',
                    del_url: 'user/health_record_release/del',
                    multi_url: 'user/health_record_release/multi',
                    import_url: 'user/health_record_release/import',
                    table: 'health_record_release',
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
                        {field: 'hospital', title: __('Hospital'), operate: 'LIKE'},
                        {field: 'is_one', title: __('Is_one'), searchList: {"1":__('Is_one 1'),"2":__('Is_one 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_two', title: __('Is_two'), searchList: {"1":__('Is_two 1'),"2":__('Is_two 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_three', title: __('Is_three'), searchList: {"1":__('Is_three 1'),"2":__('Is_three 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_four', title: __('Is_four'), searchList: {"1":__('Is_four 1'),"2":__('Is_four 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_five', title: __('Is_five'), searchList: {"1":__('Is_five 1'),"2":__('Is_five 2')}, formatter: Table.api.formatter.normal},
                        {field: 'canvas', title: __('Canvas'), operate: 'LIKE'},
                        {field: 'canvas_time', title: __('Canvas_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'name_time', title: __('Name_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'form_complate_number', title: __('Form_complate_number')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
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
                url: 'user/health_record_release/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
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
                                    url: 'user/health_record_release/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/health_record_release/destroy',
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
