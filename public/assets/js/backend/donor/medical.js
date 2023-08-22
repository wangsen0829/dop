define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'donor/medical/index' + location.search,
                    add_url: 'donor/medical/add',
                    edit_url: 'donor/medical/edit',
                    del_url: 'donor/medical/del',
                    multi_url: 'donor/medical/multi',
                    import_url: 'donor/medical/import',
                    table: 'donor_medical',
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
                        {field: 'is_hospital', title: __('Is_hospital'), searchList: {"1":__('Is_hospital 1'),"2":__('Is_hospital 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_surgeries', title: __('Is_surgeries'), searchList: {"1":__('Is_surgeries 1'),"2":__('Is_surgeries 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_drink', title: __('Is_drink'), searchList: {"1":__('Is_drink 1'),"2":__('Is_drink 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_smoke', title: __('Is_smoke'), searchList: {"1":__('Is_smoke 1'),"2":__('Is_smoke 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_menstrual_cycles', title: __('Is_menstrual_cycles'), searchList: {"1":__('Is_menstrual_cycles 1'),"2":__('Is_menstrual_cycles 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_pregnant', title: __('Is_pregnant'), searchList: {"1":__('Is_pregnant 1'),"2":__('Is_pregnant 2')}, formatter: Table.api.formatter.normal},
                        {field: 'form_complate_number', title: __('Form_complate_number')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
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
                url: 'donor/medical/recyclebin' + location.search,
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
                                    url: 'donor/medical/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'donor/medical/destroy',
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
