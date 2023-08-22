define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/pre_screen/index' + location.search,
                    add_url: 'user/pre_screen/add',
                    edit_url: 'user/pre_screen/edit',
                    del_url: 'user/pre_screen/del',
                    multi_url: 'user/pre_screen/multi',
                    import_url: 'user/pre_screen/import',
                    table: 'pre_screen',
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
                        {field: 'last_name', title: __('Last_name'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'state', title: __('State'), operate: 'LIKE'},
                        {field: 'postal_code', title: __('Postal_code'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'age', title: __('Age')},
                        {field: 'height', title: __('Height'), operate:'BETWEEN'},
                        {field: 'weight', title: __('Weight'), operate:'BETWEEN'},
                        {field: 'marital_status', title: __('Marital_status'), searchList: {"not married":__('Not married'),"married":__('Married'),"divorce":__('Divorce')}, formatter: Table.api.formatter.status},
                        {field: 'product_number', title: __('Product_number')},
                        {field: 'caesarean_number', title: __('Caesarean_number')},
                        {field: 'miscarriage_number', title: __('Miscarriage_number')},
                        {field: 'abortion_reason', title: __('Abortion_reason'), operate: 'LIKE'},
                        {field: 'is_pregnant', title: __('Is_pregnant'), searchList: {"0":__('Is_pregnant 0'),"1":__('Is_pregnant 1')}, formatter: Table.api.formatter.normal},
                        {field: 'contraceptive_measures', title: __('Contraceptive_measures'), operate: 'LIKE'},
                        {field: 'taken_drugs', title: __('Taken_drugs'), operate: 'LIKE'},
                        {field: 'current_drug_list', title: __('Current_drug_list'), operate: 'LIKE'},
                        {field: 'uterine_examination', title: __('Uterine_examination'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'form_number', title: __('Form_number')},
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
                url: 'user/pre_screen/recyclebin' + location.search,
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
                                    url: 'user/pre_screen/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/pre_screen/destroy',
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
