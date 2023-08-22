define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'notes/index/index' + location.search,
                    add_url: 'notes/index/add',
                    edit_url: 'notes/index/edit',
                    del_url: 'notes/index/del',
                    multi_url: 'notes/index/multi',
                    import_url: 'notes/index/import',
                    table: 'notes',
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
                        {field: 'id', title: __('Id'), operate: false},
                        {
                            field: 'surrogacy',
                            title: 'Surrogacy',
                            operate: false,
                            formatter: Controller.api.formatter.surrogacy
                        },
                        {field: 'form_id', title: __('Form')},
                        {field: 'content', title: __('Note'),
                            formatter: function (value, row, index){
                                return '<textarea  cols="50" rows="2" disabled>'+value+'</textarea>'
                            },operate: false
                        },
                        {field: 'admin.username', title: __('Created by')},
                        {field: 'caozuo', title: __('Status'), searchList: {"0":__('Pending Review'),"1":__('Pass'),"2":__('DQ'),"3":__('Repeated submission'),"4":__('Tried contact and waiting'),}, formatter: Table.api.formatter.status},
                        {field: 'type', title: __('Contact menthod'), searchList: {"1":__('Email'),"2":__('Phone')}, formatter: Table.api.formatter.normal},
                        // {field: 'number', title: __('Number')},
                        // {field: 'status', title: __('Able to connect'), searchList: {"1":__('Yes'),"2":__('No')}, formatter: Table.api.formatter.status},

                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,datetimeFormat:"MM-DD-YYYY HH:mm:ss"},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                url: 'notes/index/recyclebin' + location.search,
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
                                    url: 'notes/index/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'notes/index/destroy',
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
            },
            formatter: {//渲染的方法
                surrogacy: function (value, row, index) {
                    var first_name = row.form.first_name
                    if (row.form.last_name){
                        var last_name = row.form.last_name;
                    }else{
                        var last_name = '';
                    }

                    return first_name + ' '+last_name;
                },
            }
        }
    };
    return Controller;
});
