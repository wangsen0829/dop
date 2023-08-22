define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'donor/qinshu_disease/index' + location.search,
                    add_url: 'donor/qinshu_disease/add',
                    edit_url: 'donor/qinshu_disease/edit',
                    del_url: 'donor/qinshu_disease/del',
                    multi_url: 'donor/qinshu_disease/multi',
                    import_url: 'donor/qinshu_disease/import',
                    table: 'donor_qinshu_disease',
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
                        {field: 'anemia', title: __('Anemia'), operate: 'LIKE'},
                        {field: 'acne', title: __('Acne'), operate: 'LIKE'},
                        {field: 'allergies', title: __('Allergies'), operate: 'LIKE'},
                        {field: 'arthritis', title: __('Arthritis'), operate: 'LIKE'},
                        {field: 'alzheimer', title: __('Alzheimer'), operate: 'LIKE'},
                        {field: 'alcoholism', title: __('Alcoholism'), operate: 'LIKE'},
                        {field: 'blibdness', title: __('Blibdness'), operate: 'LIKE'},
                        {field: 'birth', title: __('Birth'), operate: 'LIKE'},
                        {field: 'cancer', title: __('Cancer'), operate: 'LIKE'},
                        {field: 'cerrbral', title: __('Cerrbral'), operate: 'LIKE'},
                        {field: 'chronic', title: __('Chronic'), operate: 'LIKE'},
                        {field: 'cystic', title: __('Cystic'), operate: 'LIKE'},
                        {field: 'color', title: __('Color'), operate: 'LIKE'},
                        {field: 'cataracts', title: __('Cataracts'), operate: 'LIKE'},
                        {field: 'cleft', title: __('Cleft'), operate: 'LIKE'},
                        {field: 'deafness', title: __('Deafness'), operate: 'LIKE'},
                        {field: 'down', title: __('Down'), operate: 'LIKE'},
                        {field: 'dwarfism', title: __('Dwarfism'), operate: 'LIKE'},
                        {field: 'endometriosis', title: __('Endometriosis'), operate: 'LIKE'},
                        {field: 'eczema', title: __('Eczema'), operate: 'LIKE'},
                        {field: 'emphysema', title: __('Emphysema'), operate: 'LIKE'},
                        {field: 'epliepsy', title: __('Epliepsy'), operate: 'LIKE'},
                        {field: 'heart', title: __('Heart'), operate: 'LIKE'},
                        {field: 'hypo', title: __('Hypo'), operate: 'LIKE'},
                        {field: 'hepatitis', title: __('Hepatitis'), operate: 'LIKE'},
                        {field: 'hyperactivity', title: __('Hyperactivity'), operate: 'LIKE'},
                        {field: 'hemophilia', title: __('Hemophilia'), operate: 'LIKE'},
                        {field: 'high', title: __('High'), operate: 'LIKE'},
                        {field: 'hiv', title: __('Hiv'), operate: 'LIKE'},
                        {field: 'kidney', title: __('Kidney'), operate: 'LIKE'},
                        {field: 'liver', title: __('Liver'), operate: 'LIKE'},
                        {field: 'lung', title: __('Lung'), operate: 'LIKE'},
                        {field: 'mental', title: __('Mental'), operate: 'LIKE'},
                        {field: 'migranes', title: __('Migranes'), operate: 'LIKE'},
                        {field: 'muscular', title: __('Muscular'), operate: 'LIKE'},
                        {field: 'multiple', title: __('Multiple'), operate: 'LIKE'},
                        {field: 'meningitis', title: __('Meningitis'), operate: 'LIKE'},
                        {field: 'ovarian', title: __('Ovarian'), operate: 'LIKE'},
                        {field: 'osteoporosis', title: __('Osteoporosis'), operate: 'LIKE'},
                        {field: 'sickle', title: __('Sickle'), operate: 'LIKE'},
                        {field: 'spina', title: __('Spina'), operate: 'LIKE'},
                        {field: 'stroke', title: __('Stroke'), operate: 'LIKE'},
                        {field: 'thalassemia', title: __('Thalassemia'), operate: 'LIKE'},
                        {field: 'ulcers', title: __('Ulcers'), operate: 'LIKE'},
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
                url: 'donor/qinshu_disease/recyclebin' + location.search,
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
                                    url: 'donor/qinshu_disease/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'donor/qinshu_disease/destroy',
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
