define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'surrogacy.pre_screen_form/index' + location.search,
                    add_url: 'surrogacy.pre_screen_form/add',
                    edit_url: 'surrogacy.pre_screen_form/edit',
                    del_url: 'surrogacy.pre_screen_form/del',
                    multi_url: 'surrogacy.pre_screen_form/multi',
                    import_url: 'surrogacy.pre_screen_form/import',
                    table: 'pre_screen_form',
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
                        {field: 'first_name', title: __('First_name'), operate: 'LIKE'},
                        {field: 'middle_name', title: __('Middle_name'), operate: 'LIKE'},
                        {field: 'last_name', title: __('Last_name'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'state', title: __('State'), operate: 'LIKE'},
                        {field: 'postal_code', title: __('Postal_code'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'age', title: __('Age')},
                        {field: 'height', title: __('Height'), operate:'BETWEEN'},
                        {field: 'weight', title: __('Weight'), operate:'BETWEEN'},
                        {field: 'marital_status', title: __('Marital_status'), searchList: {"0":__('Marital_status 0'),"1":__('Marital_status 1'),"2":__('Marital_status 2'),"3":__('Marital_status 3')}, formatter: Table.api.formatter.status},
                        {field: 'product_number', title: __('Product_number')},
                        {field: 'caesarean_number', title: __('Caesarean_number')},
                        {field: 'miscarriage_number', title: __('Miscarriage_number')},
                        {field: 'abortion_reason', title: __('Abortion_reason'), operate: 'LIKE'},
                        {field: 'complications_of_pregnancy', title: __('Complications_of_pregnancy'), operate: 'LIKE'},
                        {field: 'contraceptive_measures', title: __('Contraceptive_measures'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'form_complate_number', title: __('Form_complate_number')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'abortion_number', title: __('Abortion_number')},
                        {field: 'birthday_time', title: __('Birthday_time'), operate: 'LIKE'},
                        {field: 'token', title: __('Token'), operate: 'LIKE'},
                        {field: 'type', title: __('Type')},
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
                url: 'surrogacy.pre_screen_form/recyclebin' + location.search,
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
                                    url: 'surrogacy.pre_screen_form/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'surrogacy.pre_screen_form/destroy',
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
