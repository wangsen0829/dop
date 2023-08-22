define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'customcharts/chart/index' + location.search,
                    add_url: 'customcharts/chart/add',
                    edit_url: 'customcharts/chart/edit',
                    del_url: 'customcharts/chart/del',
                    table: 'customcharts_chart',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title')},
                        {field: 'subtext', title: __('Subtext')},
                        {field: 'chart_type', title: __('Chart_type'), searchList: {"pie":__('Pie'),"graph":__('Graph'),"histogram":__('Histogram')}, formatter: Table.api.formatter.normal},
                        {field: 'name', title: __('Name')},
                        {field: 'field_total', title: __('Field_total')},
                        {field: 'type_total', title: __('Type_total'), searchList: {"sum":__('Sum'),"count":__('Count')}, formatter: Table.api.formatter.normal},
                        {field: 'group_field', title: __('Group_field')},
                        {field: 'field_time', title: __('Field_time')},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'copy',
                                    title: '复制记录',
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-copy',
                                    url: $.fn.bootstrapTable.defaults.extend.add_url,
                                }
                            ]
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

                //选择表和渲染字段
                var typelist = {};
                $(document).on('change', "select[name='row[name]']", function () {
                    var that = this;
                    Fast.api.ajax({
                        url: "customcharts/totalnumber/get_field_list",
                        data: {table: $(that).val()},
                    }, function (data, ret) {
                        let mainfields  = data.fieldlist;
                        let commentlist = data.commentlist;
                        typelist    = data.typelist;//字段类型
                        Controller.api.renderselect("#c-field_total", mainfields, commentlist, typelist);//渲染数据
                        Controller.api.renderselect("#c-group_field", mainfields, commentlist, typelist);//渲染数据
                        Controller.api.renderselect("#c-field_time" , mainfields, commentlist, typelist);//渲染数据
                        Controller.api.renderselect("#c-foreign_key", mainfields, commentlist, typelist);//渲染数据
                        return false;
                    });
                    return false;
                });
                $("select[name='row[name]']").change();

                //选择时间字段
                $(document).on('change', "select[name='row[field_time]']", function () {
                    $('input[name="row[field_time_type]"]').val(typelist[$(this).val()]);
                });

                //选择关联表
                $(document).on('change', "select[name='row[join_table]']", function () {
                    var that = this;
                    if($(that).val() !== '') {
                        Fast.api.ajax({
                            url: "customcharts/totalnumber/get_field_list",
                            data: {table: $(that).val()},
                        }, function (data, ret) {
                            let mainfields  = data.fieldlist;
                            let commentlist = data.commentlist;
                            typelist    = data.typelist;//字段类型
                            Controller.api.renderselect("#c-local_key", mainfields, commentlist, typelist);//渲染数据
                            Controller.api.renderselect("#c-field_show", mainfields, commentlist, typelist);//渲染数据
                            $('.join_table').show();
                            $('.dictionary').hide();
                            return false;
                        });
                    } else {
                        $('.join_table').hide();
                        $('.dictionary').show();
                    }
                    return false;
                });
                $("select[name='row[join_table]']").change();

                //选择分组字段
                $(document).on('change', "select[name='row[group_field]']", function () {
                    var that = this;
                    if($(that).val() != '') {
                        $('.chart_type').show();
                        $('.dictionary').show();
                    } else {
                        $("select[name='row[join_table]']").selectpicker('val', '');
                        $("select[name='row[join_table]']").change();
                        $('.chart_type').hide();
                        $('.dictionary').hide();
                    }
                    return false;
                });
            },
            renderselect: function(select, data, commentlist, typelist) {
                var val = $(select).data('value');
                var html = [];
                if ('#c-group_field' == select) {
                    html.push("<option data-subtext='' value='' selected>不分组</option>");
                }
                for (var i = 0; i < data.length; i++) {
                    if ('#c-field_time' == select && typelist[data[i]] != 'int' && typelist[data[i]] != 'datetime' && typelist[data[i]] != 'date' && typelist[data[i]] != 'bigint') {
                        continue;
                    }
                    if (val == data[i]) {
                        html.push("<option data-subtext='" + commentlist[i] + "' value='" + data[i] + "' selected>" + data[i] + "</option>");
                    } else {
                        html.push("<option data-subtext='" + commentlist[i] + "' value='" + data[i] + "'>" + data[i] + "</option>");
                    }
                }
                $(select).html(html.join(""));
                $(select).selectpicker('refresh');
                if ('#c-group_field' == select) {
                    $("select[name='row[group_field]']").change();
                }
            }
        }
    };
    return Controller;
});