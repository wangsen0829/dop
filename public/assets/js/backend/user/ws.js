define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template','fast'], function ($, undefined, Backend, Table, Form, Template,Fast) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    detail_url: 'user/user/detail',
                    multi_url: '',
                }
            });

            var table = $("#table");

            Template.helper("Moment", Moment);

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                templateView: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        // {field: 'group.name', title: __('Group')},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        // {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'shenhe_content', title: 'shenhe_content', operate: 'LIKE'},
                        {field: 'examine_status', title: 'examine_status', operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        // {field: 'examine_status',title:'examine_status', searchList: {"0":"Unapproved","1":"Reviewed"}, formatter: Table.api.formatter.status},
                        // {field: 'level', title: __('Level'), operate: 'BETWEEN', sortable: true},
                        // {field: 'gender', title: __('Gender'), visible: false, searchList: {1: __('Male'), 0: __('Female')}},
                        // {field: 'score', title: __('Score'), operate: 'BETWEEN', sortable: true},
                        // {field: 'successions', title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: true},
                        // {field: 'maxsuccessions', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'logintime', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        // {field: 'loginip', title: __('Loginip'), formatter: Table.api.formatter.search},
                        {field: 'createtime', title: __('Jointime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        // {field: 'joinip', title: __('Joinip'), formatter: Table.api.formatter.search},
                        // {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ],
                ],
                //分页大小
                pageSize: 8
            });

            // 为表格绑定事件
            Table.api.bindevent(table);


            //自定义Tab筛选条件
            $('.panel-heading .nav-custom-condition a[data-toggle="tab"]', table.closest(".panel-intro")).on('shown.bs.tab', function (e) {
                var value = $(this).data("value");
                var options = table.bootstrapTable('getOptions');
                var queryParams = options.queryParams;
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    //这一行必须要存在,否则在点击下一页时会丢失搜索栏数据
                    params = queryParams(params);

                    //如果希望追加搜索条件,可使用
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    // var op = params.op ? JSON.parse(params.op) : {};
                    if (value) {
                        //这里可以自定义多个筛选条件
                        filter.examine_status = value;
                        // op.admin_id = '=';
                    } else {
                        //选全部时要移除相应的条件
                        delete filter.admin_id;
                    }

                    params.filter = JSON.stringify(filter);
                    // params.op = JSON.stringify(op);

                    //如果希望忽略搜索栏搜索条件,可使用
                    //params.filter = JSON.stringify(value?{admin_id: value}:{});
                    //params.op = JSON.stringify(value?{admin_id: '='}:{});
                    return params;
                };

                table.trigger("uncheckbox");
                table.bootstrapTable('refresh', {pageNumber: 1});
                return false;
            });


            //指定搜索条件
            $(document).on("click", ".btn-toggle-view", function () {
                var options = table.bootstrapTable('getOptions');
                table.bootstrapTable('refreshOptions', {templateView: !options.templateView});
            });




            //审核
            $(document).on("click", ".shenhe", function () {
                var id = $(this).data('id');
                var content = $(this).data('shenhe_content');
                var examine_status = $(this).data('examine_status');
                var html = '';
                html += '<div>';
                html += ' <label >Status:</label>';
                html += '<div class="radio">';

                if (examine_status ==1 ){
                    html += '<label><input checked="checked" name="examine_status" type="radio" value="1" >Reviewed</label> ';
                }else{
                    html += '<label><input  name="examine_status" type="radio" value="1" >Reviewed</label> ';
                }

                if (examine_status==2){
                    html += '<label><input  checked="checked" name="examine_status" type="radio" value="2"  >Unapproved</label> ';
                }else{
                    html += '<label><input  name="examine_status" type="radio" value="2"  >Unapproved</label> ';
                }
                html += ' </div>';
                html += ' </div>';
                html += ' <div>';
                html += '<label >Content:</label>';
                html += '<div>';
                html += '<textarea name="txt_remark" id="remark" placeholder="If the review fails, please fill in the reason" style="width:400px;height:100px;">'+content+'</textarea>';
                html += ' </div>';
                html += ' </div>';
                layer.prompt({
                    formType : 0,
                    title : 'To Examine',
                    content:html,
                    btn:['yes','no'],
                    yes: function (index, layero) {
                        var examine_status = $("input[name='examine_status']:checked").val();//获取多行文本框的值
                        var content = $('#remark').val();//获取多行文本框的值
                        $.ajax({
                            url:'user/user/shenhe',
                            type:'GET',
                            dataType:"json",
                            data:{id,content,examine_status},
                            async:false,
                            success:function(res){
                                console.log(res);
                            }
                        });
                        layer.close(index);
                        window.parent.location.reload();
                    }
                });
            });

        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            // $('.datetimepicker').datetimepicker();
            // Form.api.bindevent($("#basic-form"));
            Controller.api.bindevent();
        },
        detail:function () {
            //点击详情
            $(document).on("click", ".btn-detail[data-id]", function () {
                // Backend.api.open('user/user/phone/ids/' + $(this).data('id'), __('Photo wall'));
                top.Fast.api.open('user/user/photo/ids/' + $(this).data('id'), __('Photo wall'),{area:["60%", "80%"]});
            });
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                url: function (value, row, index) {
                    return '<div class="input-group input-group-sm" style="width:250px;"><input type="text" class="form-control input-sm" value="' + value + '"><span class="input-group-btn input-group-sm"><a href="' + value + '" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-link"></i></a></span></div>';
                },
                ip: function (value, row, index) {
                    return '<a class="btn btn-xs btn-ip bg-success"><i class="fa fa-map-marker"></i> ' + value + '</a>';
                },
                browser: function (value, row, index) {
                    //这里我们直接使用row的数据
                    return '<a class="btn btn-xs btn-browser">' + row.useragent.split(" ")[0] + '</a>';
                }
            },
            events: {
                ip: {
                    'click .btn-ip': function (e, value, row, index) {
                        var options = $("#table").bootstrapTable('getOptions');
                        //这里我们手动将数据填充到表单然后提交
                        $("#commonSearchContent_" + options.idTable + " form [name='ip']").val(value);
                        $("#commonSearchContent_" + options.idTable + " form").trigger('submit');
                        Toastr.info("执行了自定义搜索操作");
                    }
                },
                browser: {
                    'click .btn-browser': function (e, value, row, index) {
                        Layer.alert("该行数据为: <code>" + JSON.stringify(row) + "</code>");
                    }
                }
            }
        }
    };
    return Controller;
});