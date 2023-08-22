define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/personal_info/index' + location.search,
                    add_url: 'user/personal_info/add',
                    edit_url: 'user/personal_info/edit',
                    del_url: 'user/personal_info/del',
                    multi_url: 'user/personal_info/multi',
                    import_url: 'user/personal_info/import',
                    table: 'personal_info',
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
                        {field: 'birthday_time', title: __('Birthday_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'blood_type', title: __('Blood_type'), searchList: {"0":__('Blood_type 0'),"1":__('Blood_type 1'),"2":__('Blood_type 2'),"3":__('Blood_type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'highest_education', title: __('Highest_education'), operate: 'LIKE'},
                        {field: 'source_of_income', title: __('Source_of_income'), operate: 'LIKE'},
                        {field: 'race', title: __('Race'), searchList: {"0":__('Race 0'),"1":__('Race 1'),"2":__('Race 2'),"3":__('Race 3')}, formatter: Table.api.formatter.normal},
                        {field: 'sexual_orientation', title: __('Sexual_orientation'), searchList: {"0":__('Sexual_orientation 0'),"1":__('Sexual_orientation 1')}, formatter: Table.api.formatter.normal},
                        {field: 'religion', title: __('Religion'), operate: 'LIKE'},
                        {field: 'family_attitude', title: __('Family_attitude'), searchList: {"yes":__('Yes'),"no":__('No')}, formatter: Table.api.formatter.normal},
                        {field: 'surrogate_number', title: __('Surrogate_number')},
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
                url: 'user/personal_info/recyclebin' + location.search,
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
                                    url: 'user/personal_info/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/personal_info/destroy',
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
