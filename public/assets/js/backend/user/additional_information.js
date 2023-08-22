define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/additional_information/index' + location.search,
                    add_url: 'user/additional_information/add',
                    edit_url: 'user/additional_information/edit',
                    del_url: 'user/additional_information/del',
                    multi_url: 'user/additional_information/multi',
                    import_url: 'user/additional_information/import',
                    table: 'additional_information',
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
                        {field: 'driver_license', title: __('Driver_license'), operate: 'LIKE'},
                        {field: 'issued_state', title: __('Issued_state'), operate: 'LIKE'},
                        {field: 'driver_license_image', title: __('Driver_license_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'social_security_number', title: __('Social_security_number'), operate: 'LIKE'},
                        {field: 'emergency_contact_first_name', title: __('Emergency_contact_first_name'), operate: 'LIKE'},
                        {field: 'emergency_contact_last_name', title: __('Emergency_contact_last_name'), operate: 'LIKE'},
                        {field: 'emergency_contact_phone_number', title: __('Emergency_contact_phone_number'), operate: 'LIKE'},
                        {field: 'emergency_contact_email', title: __('Emergency_contact_email'), operate: 'LIKE'},
                        {field: 'is_health_care', title: __('Is_health_care'), searchList: {"1":__('Is_health_care 1'),"2":__('Is_health_care 2')}, formatter: Table.api.formatter.normal},
                        {field: 'front_side_image', title: __('Front_side_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'back_side_image', title: __('Back_side_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
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
                url: 'user/additional_information/recyclebin' + location.search,
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
                                    url: 'user/additional_information/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/additional_information/destroy',
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
