define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/about_surrogacy/index' + location.search,
                    add_url: 'user/about_surrogacy/add',
                    edit_url: 'user/about_surrogacy/edit',
                    del_url: 'user/about_surrogacy/del',
                    multi_url: 'user/about_surrogacy/multi',
                    import_url: 'user/about_surrogacy/import',
                    table: 'about_surrogacy',
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
                        {field: 'is_surrogate', title: __('Is_surrogate'), searchList: {"1":__('Is_surrogate 1'),"2":__('Is_surrogate 2')}, formatter: Table.api.formatter.normal},
                        {field: 'begin_surrogate', title: __('Begin_surrogate'), operate: 'LIKE'},
                        {field: 'apply_surrogate_agency', title: __('Apply_surrogate_agency'), operate: 'LIKE'},
                        {field: 'embryo_transfer_number', title: __('Embryo_transfer_number')},
                        {field: 'is_conceive_twins', title: __('Is_conceive_twins'), searchList: {"1":__('Is_conceive_twins 1'),"2":__('Is_conceive_twins 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_fetal_reduction', title: __('Is_fetal_reduction'), searchList: {"1":__('Is_fetal_reduction 1'),"2":__('Is_fetal_reduction 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_induced_abortion', title: __('Is_induced_abortion'), searchList: {"1":__('Is_induced_abortion 1'),"2":__('Is_induced_abortion 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_cvs', title: __('Is_cvs'), searchList: {"1":__('Is_cvs 1'),"2":__('Is_cvs 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_tom', title: __('Is_tom'), searchList: {"1":__('Is_tom 1'),"2":__('Is_tom 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_meet_parents', title: __('Is_meet_parents'), searchList: {"1":__('Is_meet_parents 1'),"2":__('Is_meet_parents 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_relinquish', title: __('Is_relinquish'), searchList: {"1":__('Is_relinquish 1'),"2":__('Is_relinquish 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_ss', title: __('Is_ss'), searchList: {"1":__('Is_ss 1'),"2":__('Is_ss 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_breast_feed', title: __('Is_breast_feed'), searchList: {"1":__('Is_breast_feed 1'),"2":__('Is_breast_feed 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_sexual_life', title: __('Is_sexual_life'), searchList: {"1":__('Is_sexual_life 1'),"2":__('Is_sexual_life 2')}, formatter: Table.api.formatter.normal},
                        {field: 'family_content', title: __('Family_content'), operate: 'LIKE'},
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
                url: 'user/about_surrogacy/recyclebin' + location.search,
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
                                    url: 'user/about_surrogacy/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/about_surrogacy/destroy',
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
