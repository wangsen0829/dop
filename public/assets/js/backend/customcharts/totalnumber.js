define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'customcharts/totalnumber/index' + location.search,
                    add_url: 'customcharts/totalnumber/add',
                    edit_url: 'customcharts/totalnumber/edit',
                    del_url: 'customcharts/totalnumber/del',
                    table: 'customcharts_total_number',
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
                        {field: 'name', title: __('Name')},
                        {field: 'field_total', title: __('Field_total')},
                        {field: 'type_total', title: __('Type_total'), searchList: {"sum":__('Sum'),"count":__('Count')}, formatter: Table.api.formatter.normal},
                        {field: 'field_time', title: __('Field_time')},
                        {field: 'type_time', title: __('Type_time'), searchList: {"today":__('Today'),"week":__('Week'),"month":__('Month'),"all":__('All')}, formatter: Table.api.formatter.normal},
                        {field: 'icon', title: __('Icon'), formatter: Table.api.formatter.icon},
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
                
                //颜色选择器
                var refreshStyle = function () {
                    var style = [];
                    if ($(".btn-color").hasClass("active")) {
                        style.push($(".btn-color").data("color"));
                    }
                    $("input[name='row[icon_color]']").val(style.join("|"));
                };
                if ($(".btn-color").hasClass("active")) {
                    style.push($(".btn-color").data("color"));
                }
                require(['jquery-colorpicker'], function () {
                    $('.colorpicker').colorpicker({
                        color: function () {
                            var color = "#000000";
                            var rgb = $("#c-icon_color").css('color').match(/^rgb\(((\d+),\s*(\d+),\s*(\d+))\)$/);
                            if (rgb) {
                                color = rgb[1];
                            }
                            return color;
                        }
                    }, function (event, obj) {
                        $("#c-icon_color").css('color', '#' + obj.hex);
                        $(event).addClass("active").data("color", '#' + obj.hex);
                        refreshStyle();
                    }, function (event) {
                        $("#c-icon_color").css('color', 'inherit');
                        $(event).removeClass("active");
                        refreshStyle();
                    });
                });

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
                        Controller.api.renderselect("#c-field_time" , mainfields, commentlist, typelist);//渲染数据
                        return false;
                    });
                    return false;
                });
                $("select[name='row[name]']").change();

                //选择时间字段
                $(document).on('change', "select[name='row[field_time]']", function () {
                    $('input[name="row[field_time_type]"]').val(typelist[$(this).val()]);
                });

                //搜索图标
                var iconlist = [];
                var iconfunc = function () {
                    Layer.open({
                        type: 1,
                        area: ['99%', '98%'], //宽高
                        content: Template('chooseicontpl', {iconlist: iconlist})
                    });
                };
                $(document).on('click', ".btn-search-icon", function () {
                    if (iconlist.length == 0) {
                        $.get(Config.site.cdnurl + "/assets/libs/font-awesome/less/variables.less", function (ret) {
                            var exp = /fa-var-(.*):/ig;
                            var result;
                            while ((result = exp.exec(ret)) != null) {
                                iconlist.push(result[1]);
                            }
                            iconfunc();
                        });
                    } else {
                        iconfunc();
                    }
                });
                $(document).on('click', '#chooseicon ul li', function () {
                    $("input[name='row[icon]']").val('fa fa-' + $(this).data("font"));
                    Layer.closeAll();
                });
                $(document).on('keyup', 'input.js-icon-search', function () {
                    $("#chooseicon ul li").show();
                    if ($(this).val() != '') {
                        $("#chooseicon ul li:not([data-font*='" + $(this).val() + "'])").hide();
                    }
                });
            },
            renderselect: function(select, data, commentlist, typelist) {
                var val = $(select).data('value');
                var html = [];
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
            }
        }
    };
    return Controller;
});