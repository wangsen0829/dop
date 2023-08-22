define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'surrogacy.lead/index' + location.search,
                    add_url: 'surrogacy.lead/add',
                    edit_url: 'surrogacy.lead/edit',
                    del_url: 'surrogacy.lead/del',
                    multi_url: 'surrogacy.lead/multi',
                    import_url: 'surrogacy.lead/import',
                    table: 'surrogacy_lead',
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
                        {field: 'form_id', title: __('Form_id')},
                        {field: 'birthday_time', title: __('Birthday_time'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'state', title: __('State'), operate: 'LIKE'},
                        {field: 'postal_code', title: __('Postal_code'), operate: 'LIKE'},
                        {field: 'age', title: __('Age'), operate: 'LIKE'},
                        {field: 'height', title: __('Height'), operate: 'LIKE'},
                        {field: 'height_ft', title: __('Height_ft'), operate: 'LIKE'},
                        {field: 'height_in', title: __('Height_in'), operate: 'LIKE'},
                        {field: 'bmi', title: __('Bmi'), operate: 'LIKE'},
                        {field: 'weight', title: __('Weight'), operate: 'LIKE'},
                        {field: 'marital_status', title: __('Marital_status'), searchList: {"1":__('Marital_status 1'),"2":__('Marital_status 2')}, formatter: Table.api.formatter.status},
                        {field: 'is_us', title: __('Is_us')},
                        {field: 'product_number', title: __('Product_number'), operate: 'LIKE'},
                        {field: 'caesarean_number', title: __('Caesarean_number'), operate: 'LIKE'},
                        {field: 'miscarriage_number', title: __('Miscarriage_number'), operate: 'LIKE'},
                        {field: 'abortion_number', title: __('Abortion_number'), operate: 'LIKE'},
                        {field: 'is_abortion_reason', title: __('Is_abortion_reason')},
                        {field: 'abortion_reason', title: __('Abortion_reason'), operate: 'LIKE'},
                        {field: 'is_complications_of_pregnancy', title: __('Is_complications_of_pregnancy')},
                        {field: 'complications_of_pregnancy', title: __('Complications_of_pregnancy'), operate: 'LIKE'},
                        {field: 'contraceptive_measures', title: __('Contraceptive_measures'), operate: 'LIKE'},
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
                url: 'surrogacy.lead/recyclebin' + location.search,
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
                                    url: 'surrogacy.lead/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'surrogacy.lead/destroy',
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
