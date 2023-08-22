define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'donor/personal/index' + location.search,
                    add_url: 'donor/personal/add',
                    edit_url: 'donor/personal/edit',
                    del_url: 'donor/personal/del',
                    multi_url: 'donor/personal/multi',
                    import_url: 'donor/personal/import',
                    table: 'donor_personal',
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
                        {field: 'eye_color', title: __('Eye_color'), operate: 'LIKE'},
                        {field: 'natural_hair_type', title: __('Natural_hair_type'), operate: 'LIKE'},
                        {field: 'naturl_hair_color', title: __('Naturl_hair_color'), operate: 'LIKE'},
                        {field: 'skin_tone', title: __('Skin_tone'), operate: 'LIKE'},
                        {field: 'blood_type', title: __('Blood_type'), operate: 'LIKE'},
                        {field: 'predominant_hand', title: __('Predominant_hand'), operate: 'LIKE'},
                        {field: 'is_dimples', title: __('Is_dimples'), searchList: {"1":__('Is_dimples 1'),"2":__('Is_dimples 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_eye_glasses', title: __('Is_eye_glasses'), searchList: {"1":__('Is_eye_glasses 1'),"2":__('Is_eye_glasses 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_had_braces', title: __('Is_had_braces'), searchList: {"1":__('Is_had_braces 1'),"2":__('Is_had_braces 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_tattoos', title: __('Is_tattoos'), searchList: {"1":__('Is_tattoos 1'),"2":__('Is_tattoos 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_piercings', title: __('Is_piercings'), searchList: {"1":__('Is_piercings 1'),"2":__('Is_piercings 2')}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'form_complate_number', title: __('Form_complate_number')},
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
                url: 'donor/personal/recyclebin' + location.search,
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
                                    url: 'donor/personal/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'donor/personal/destroy',
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
