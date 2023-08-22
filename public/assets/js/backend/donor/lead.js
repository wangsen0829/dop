define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'donor/lead/index' + location.search,
                    add_url: 'donor/lead/add',
                    edit_url: 'donor/lead/edit',
                    del_url: 'donor/lead/del',
                    multi_url: 'donor/lead/multi',
                    import_url: 'donor/lead/import',
                    table: 'donor_lead',
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
                        {field: 'city_state', title: __('City_state'), operate: 'LIKE'},
                        {field: 'age', title: __('Age'), operate: 'LIKE'},
                        {field: 'height', title: __('Height'), operate: 'LIKE'},
                        {field: 'height_ft', title: __('Height_ft'), operate: 'LIKE'},
                        {field: 'height_in', title: __('Height_in'), operate: 'LIKE'},
                        {field: 'weight', title: __('Weight'), operate: 'LIKE'},
                        {field: 'ethnicity', title: __('Ethnicity'), operate: 'LIKE'},
                        {field: 'blood_type', title: __('Blood_type'), operate: 'LIKE'},
                        {field: 'place_of_birth', title: __('Place_of_birth'), operate: 'LIKE'},
                        {field: 'highest_education', title: __('Highest_education'), operate: 'LIKE'},
                        {field: 'occupation', title: __('Occupation'), operate: 'LIKE'},
                        {field: 'is_injections', title: __('Is_injections'), searchList: {"1":__('Is_injections 1'),"2":__('Is_injections 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_attend_appointment', title: __('Is_attend_appointment'), searchList: {"1":__('Is_attend_appointment 1'),"2":__('Is_attend_appointment 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_any_medications', title: __('Is_any_medications'), searchList: {"1":__('Is_any_medications 1'),"2":__('Is_any_medications 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_donated', title: __('Is_donated'), searchList: {"1":__('Is_donated 1'),"2":__('Is_donated 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_egg_donor', title: __('Is_egg_donor'), searchList: {"1":__('Is_egg_donor 1'),"2":__('Is_egg_donor 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_plan', title: __('Is_plan'), searchList: {"1":__('Is_plan 1'),"2":__('Is_plan 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_smoke', title: __('Is_smoke'), searchList: {"1":__('Is_smoke 1'),"2":__('Is_smoke 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_drink', title: __('Is_drink'), searchList: {"1":__('Is_drink 1'),"2":__('Is_drink 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_menstrual_regular', title: __('Is_menstrual_regular'), searchList: {"1":__('Is_menstrual_regular 1'),"2":__('Is_menstrual_regular 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_illicit_drugs', title: __('Is_illicit_drugs'), searchList: {"1":__('Is_illicit_drugs 1'),"2":__('Is_illicit_drugs 2')}, formatter: Table.api.formatter.normal},
                        {field: 'crearetime', title: __('Crearetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
                url: 'donor/lead/recyclebin' + location.search,
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
                                    url: 'donor/lead/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'donor/lead/destroy',
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
