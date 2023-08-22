define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/medical_information/index' + location.search,
                    add_url: 'user/medical_information/add',
                    edit_url: 'user/medical_information/edit',
                    del_url: 'user/medical_information/del',
                    multi_url: 'user/medical_information/multi',
                    import_url: 'user/medical_information/import',
                    table: 'medical_information',
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
                        {field: 'insurance_company', title: __('Insurance_company'), operate: 'LIKE'},
                        {field: 'covid', title: __('Covid'), searchList: {"0":__('Covid 0'),"1":__('Covid 1'),"2":__('Covid 2'),"3":__('Covid 3'),"4":__('Covid 4')}, formatter: Table.api.formatter.normal},
                        {field: 'obgyn_visit_time', title: __('Obgyn_visit_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'pap_smear_time', title: __('Pap_smear_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'hepatitis_b_vaccine', title: __('Hepatitis_b_vaccine'), operate: 'LIKE'},
                        {field: 'varicella_vaccine', title: __('Varicella_vaccine'), operate: 'LIKE'},
                        {field: 'menstruation_start_time', title: __('Menstruation_start_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'menstruation_long_time', title: __('Menstruation_long_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'is_menstruation_regular', title: __('Is_menstruation_regular'), searchList: {"1":__('Is_menstruation_regular 1'),"2":__('Is_menstruation_regular 2')}, formatter: Table.api.formatter.normal},
                        {field: 'menstruation_two', title: __('Menstruation_two'), operate: 'LIKE'},
                        {field: 'birth_control_method', title: __('Birth_control_method'), operate: 'LIKE'},
                        {field: 'is_any_infectious_disease', title: __('Is_any_infectious_disease'), searchList: {"1":__('Is_any_infectious_disease 1'),"2":__('Is_any_infectious_disease 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_any_genetic_disease', title: __('Is_any_genetic_disease'), searchList: {"1":__('Is_any_genetic_disease 1'),"2":__('Is_any_genetic_disease 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_mental_illness', title: __('Is_mental_illness'), searchList: {"1":__('Is_mental_illness 1'),"2":__('Is_mental_illness 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_other_disease', title: __('Is_other_disease'), searchList: {"1":__('Is_other_disease 1'),"2":__('Is_other_disease 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_take_any_medicine', title: __('Is_take_any_medicine'), searchList: {"1":__('Is_take_any_medicine 1'),"2":__('Is_take_any_medicine 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_allergic_medication', title: __('Is_allergic_medication'), searchList: {"1":__('Is_allergic_medication 1'),"2":__('Is_allergic_medication 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_weightc_change', title: __('Is_weightc_change'), searchList: {"1":__('Is_weightc_change 1'),"2":__('Is_weightc_change 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_any_surgeries', title: __('Is_any_surgeries'), searchList: {"1":__('Is_any_surgeries 1'),"2":__('Is_any_surgeries 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_twins', title: __('Is_twins'), searchList: {"1":__('Is_twins 1'),"2":__('Is_twins 2')}, formatter: Table.api.formatter.normal},
                        {field: 'surrogate_content', title: __('Surrogate_content'), operate: 'LIKE'},
                        {field: 'many_children', title: __('Many_children'), operate: 'LIKE'},
                        {field: 'is_children_live', title: __('Is_children_live'), searchList: {"1":__('Is_children_live 1'),"2":__('Is_children_live 2')}, formatter: Table.api.formatter.normal},
                        {field: 'is_having_more_children', title: __('Is_having_more_children'), searchList: {"1":__('Is_having_more_children 1'),"2":__('Is_having_more_children 2')}, formatter: Table.api.formatter.normal},
                        {field: 'any_5_medication', title: __('Any_5_medication'), operate: 'LIKE'},
                        {field: 'any_surgeries', title: __('Any_surgeries'), operate: 'LIKE'},
                        {field: 'any_diseases', title: __('Any_diseases'), searchList: {"0":__('Any_diseases 0'),"1":__('Any_diseases 1'),"2":__('Any_diseases 2'),"3":__('Any_diseases 3'),"4":__('Any_diseases 4'),"5":__('Any_diseases 5'),"6":__('Any_diseases 6'),"7":__('Any_diseases 7'),"8":__('Any_diseases 8')}, formatter: Table.api.formatter.normal},
                        {field: 'form_complate_number', title: __('Form_complate_number')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
