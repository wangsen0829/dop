define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/pre_screen_form/index' + location.search,
                    add_url: 'user/pre_screen_form/add',
                    edit_url: 'user/pre_screen_form/edit',
                    del_url: 'user/pre_screen_form/del',
                    multi_url: 'user/pre_screen_form/multi',
                    import_url: 'user/pre_screen_form/import',
                    table: 'pre_screen_form',
                }
            });
            var table = $("#table");

            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='lead_source']", form).addClass("selectpage").data("source", "lead_source/index").data("primaryKey", "name").data("field", "name").data("orderBy", "id asc");
                $("input[name='admin_id']", form).addClass("selectpage").data("source", "auth/admin/index").data("primaryKey", "id").data("field", "username").data("orderBy", "id asc");
                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });



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
                        {field: 'id', title: __('Id'),operate:false},
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"0":__('New lead'),"1":__('Pre-screened'),"2":__('DQed'),"3":__('Repeated submission'),"4":__('Tried contact and waiting')},
                            formatter: Table.api.formatter.status,
                        },
                        {
                            field: 'username',
                            title: 'Name',
                            operate: false,
                            formatter: Controller.api.formatter.username
                        },
                        {field: 'email', title: 'Email', operate: 'LIKE'},
                        {field: 'mobile', title: 'Mobile', operate: 'LIKE',
                            formatter: function (value, row, index){
                                var phone = value;
                                var html = '';
                                if (value.indexOf('(')!=-1||value.indexOf('-')!=-1){
                                    var value = value.replace('(','');
                                    value = value.replace(')','');
                                    value = value.replace('-','');
                                    value = '+1'+value.replace(' ','');
                                    value = value.slice(2);
                                    html += '<span style="padding-right:3px">+1</span>';
                                }else if (value.substr(0,2)=="+1"){
                                    value = value.slice(2);
                                    html += '<span style="padding-right:3px">+1</span>';
                                }else if (value.length==10){
                                    value = phone
                                    html += '<span style="padding-right:3px">+1</span>';
                                }
                                html += '<span>'+value+'</span><a href="tel:'+phone+'" style="padding-left:5px;"><i class="fa fa-phone" style="color: #18bc9c;font-size: 16px;"></i></a>'
                                return html;
                            }
                            },
                        {
                            field: 'service',
                            title: __('Service'),
                            searchList: {"1":__('Be a surrogate'),"2":__('Be an egg donor'),"3":__('Be an Sperm donation'),"4":__('Find a surrogate'),"5":__('Other')},
                            formatter: Table.api.formatter.status
                        },
                        // {field: 'age', title: 'Age',   formatter: function (value, row, index){
                        //         if (value==null||value==0){
                        //           return '-'
                        //         }
                        //         return value
                        //     }},

                        {field: 'lead_source', title: 'Lead source',addclass: 'selectpage', operate: 'LIKE'},
                        {field: 'referral', title: 'Referral', operate: 'LIKE'},
                        {
                            field: 'admin_id',
                            title: 'Owner',
                            operate: 'LIKE',
                            addclass: 'selectpage',
                            formatter: Controller.api.formatter.admin
                        },
                        {field: 'follow_ups', title:'# of follow-ups',operate:'LIKE',formatter: function (value, row, index){
                                if(value==0){
                                    return "-";
                                }else{
                                    return value;
                                }
                            },operate:false},
                        {
                            field: 'updatetime',
                            title: __('Note time'),
                            operate:'RANGE',
                            addclass:'datetimerange',
                            autocomplete:false,
                            visible:false
                            // formatter: Table.api.formatter.datetime
                            // datetimeFormat:"MM-DD-YYYY HH:mm:ss"
                        },
                        {
                            field: 'createtime',
                            title: __('Createtime'),
                            operate:'RANGE',
                            addclass:'datetimerange',
                            autocomplete:false,
                            // formatter: Table.api.formatter.datetime
                            // datetimeFormat:"MM-DD-YYYY HH:mm:ss"
                        },
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {name: 'detail',
                                    text: '',
                                    title: 'Detail',
                                    icon: 'fa fa-list',
                                    extend: 'data-area=\'["80%", "80%"]\'',
                                    classname: 'btn btn-xs btn-primary btn-dialog btn-de',
                                    url: 'user/pre_screen_form/detail'},
                                {name: 'Note',
                                    text: 'Note',
                                    title: 'Note',
                                    icon: 'fa fa-commenting-o',
                                    extend: 'data-area=\'["90%", "90%"]\'',
                                    classname: 'btn btn-xs btn-warning  btn-dialog btn-de',
                                    url: 'user/pre_screen_form/notes'
                                },
                                {
                                    name: '',
                                    text: 'Pass',
                                    title: 'Approved',
                                    confirm: 'Are you sure the review has passed？',
                                    classname: 'btn btn-xs btn-info btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'user/pre_screen_form/examine',
                                    success: function (data, ret) {
                                        if (ret.code==1){
                                            layer.msg(ret.msg)
                                            $(".btn-refresh").trigger("click"); //刷新页面
                                        }else {
                                            layer.msg(ret.msg)
                                        }
                                    }
                                },
                                {
                                    name: '',
                                    text: __('DQ'),
                                    title: __('Audit reject'),
                                    classname: 'btn btn-xs btn-danger btn-click',
                                    icon: 'fa fa-close',
                                    click: function (e,s) {
                                        var ids = s.id;
                                        Layer.open({
                                            title:'DQ content',
                                            content: Template("dq", {}),
                                            btn: [__('OK')],
                                            yes: function (index, layero) {
                                                var dq_content = $("#dq_content", layero).val();
                                                if (!dq_content){
                                                    layer.msg('Please fill in the reason')
                                                    return false;
                                                }
                                                Fast.api.ajax({
                                                    url: "user/pre_screen_form/refuse",
                                                    type: "post",
                                                    data: {dq_content,ids},
                                                }, function (a,ret) {

                                                });
                                                table.bootstrapTable('refresh', {});
                                                // Toastr.info("DQed");
                                                Layer.close(index);
                                            },
                                            success: function (a, b) {
                                            }
                                        });
                                    }
                                },
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams: function (params) {
                    //这里可以追加搜索条件
                    var filter = JSON.parse(params.filter);
                    var op = JSON.parse(params.op);
                    //这里可以动态赋值，比如从URL中获取admin_id的值，filter.admin_id=Fast.api.query('admin_id');
                    filter.status = '0';
                    op.status = "=";
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                },
            });
            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on('click', '.btn-classify', function () {
                var ids = Table.api.selectedids(table);
                Layer.open({
                    title: __('Classify'),
                    content: Template("typetpl", {}),
                    btn: [__('OK')],
                    yes: function (index, layero) {
                        var category = $("select[name='category']", layero).val();
                        if (!category){
                            Layer.msg('Please select a follow-up person');
                            return false;
                        }
                        Fast.api.ajax({
                            url: "user/pre_screen_form/fenpei",
                            type: "post",
                            data: {category: category, ids: ids.join(',')},
                        }, function () {
                            table.bootstrapTable('refresh', {});
                            Layer.close(index);
                        });
                    },
                    success: function (layero, index) {
                    }
                });
            });

            //重复字段搜索
            $(document).on("click", ".btn-repeat", function () {
                var value = $(this).data("email");
                var container = $("#table").data("bootstrap.table").$container;
                var options = $("#table").bootstrapTable('getOptions');
                $("form.form-commonsearch [name='email']", container).val(value);
                $("form.form-commonsearch", container).trigger('submit');
                Toastr.info("Find the same data");
            });
            //重置按钮
            $(document).on("click", ".btn-refresh", function() {
                // let urlSearch = new URLSearchParams(location.search);
                // urlSearch.delete('status');
                // location.search = '?'+urlSearch.toString()
                $(".form-commonsearch [type=reset]").trigger("click");
            });

            $('select[name="status"]').change(function (e) {
                var value = $(this).val();
                // alert(value)
                var options = table.bootstrapTable('getOptions');
                var queryParams = options.queryParams;
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    //这一行必须要存在,否则在点击下一页时会丢失搜索栏数据
                    params = queryParams(params);
                    //如果希望追加搜索条件,可使用
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};

                    if (value||value=='0') {
                        // delete filter.id;
                        //这里可以自定义多个筛选条件
                        filter.status = value;
                    } else {
                        // delete filter.id;
                        //选全部时要移除相应的条件
                        delete filter.status;
                    }

                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
            });




            // 绑定TAB事件
            $('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var value = $(this).data("value");
                var options = table.bootstrapTable('getOptions');
                var queryParams = options.queryParams;
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    //这一行必须要存在,否则在点击下一页时会丢失搜索栏数据
                    params = queryParams(params);
                    //如果希望追加搜索条件,可使用
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};

                    if (value||value=='0') {
                        // delete filter.id;
                        //这里可以自定义多个筛选条件
                        filter.status = value;
                    } else {
                        // delete filter.id;
                        //选全部时要移除相应的条件
                        delete filter.status;
                    }

                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
            });
            function contact(){
                var ids = '';
                $.ajax({
                    url:'user/pre_screen_form/status_contact',
                    type:'get',
                    dataType:"json",
                    async:false,
                    success:function(res){
                        ids = res.data;
                    }
                });
                return ids;
            }

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
                url: 'user/pre_screen_form/recyclebin' + location.search,
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
                                    url: 'user/pre_screen_form/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/pre_screen_form/destroy',
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
        notes: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/pre_screen_form/notes/ids/'+$('#ids').val() + location.search,
                    del_url: 'user/pre_screen_form/note_del/form_id/'+$('#ids').val() + location.search,
                    table: 'notes',
                }
            });

            var table = $("#notes");

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
                        // {field: 'id', title: __('Id')},
                        {field: 'admin.username', title: __('Created by')},
                        {field: 'content', title: __('Note'), operate: 'LIKE',
                            formatter: function (value, row, index){
                                return '<textarea  cols="50" rows="2" disabled>'+value+'</textarea>'
                            }
                        },
                        {field: 'caozuo', title: __('Status'), searchList: {"0":__('New lead'),"1":__('Pre-screened'),"2":__('DQed'),"3":__('Repeated submission'),"4":__('Tried contact and waiting')}, formatter: Table.api.formatter.status},
                        {field: 'type', title: __('Contact method'), searchList: {"1":__('Phone'),"2":__('Email')}, formatter: Table.api.formatter.normal},
                        {field: 'nexttime', title: __('Next time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,datetimeFormat:"MM-DD-YYYY",
                            // formatter: function (value, row, index){
                            //     if(value==0||value=='null'){
                            //         return "-";
                            //     }else{
                            //         return value;
                            //     }
                            // }
                        },
                        // {field: 'status', title: __('Able to connect'), searchList: {"1":__('Yes'),"2":__('No')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,datetimeFormat:"MM-DD-YYYY  HH:mm:ss"},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],

            });

            $('.btn-save').click(function () {
                var form_id = $('#form_id').val();
                var email = $('#email').val();
                var content = $('#record-content').val();
                if (!content){
                    layer.msg('Content cannot be empty');
                    return false;
                }
                var caozuo = $('#caozuo option:selected').val();
                var type = $('#type option:selected').val();
                var status = $('#status option:selected').val();
                var nexttime = $('#nexttime').val();
                if (caozuo==4&&nexttime==''){
                    layer.msg('Next follow-up time cannot be empty');
                    return false;
                }
                // alert(nexttime);return false;
                $.ajax({
                    url:'user/pre_screen_form/notes_add',
                    type:'get',
                    dataType:"json",
                    data:{form_id,email,content,caozuo,type,status,nexttime},
                    async:false,
                    success:function(res){
                        layer.msg(res.msg)
                        table.bootstrapTable('refresh', {});
                    }
                });
            })
     
            // 为表格绑定事件
            Table.api.bindevent(table);

            Form.api.bindevent($("#notes1"));
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                username: function (value, row, index) {
                  var first_name = row.first_name
                    var is_repeat = row.is_repeat;
                    var email = row.email;
                    if (row.last_name){
                        var last_name = row.last_name;
                    }else{
                        var last_name = '';
                    }
                    var username = last_name + ' '+first_name;
                    if (is_repeat==2){
                        var html = "<div>"+username+"</div><div class='btn btn-xs btn-primary btn-repeat' title='Duplicate data' data-email='"+email+"'> repeat</div>"
                        return html;
                    }else {
                        return username;
                    }

                },
                admin: function (value, row, index) {
                    var username = row.admin.username
                    if (username){
                        return username;
                    }else {
                        return '-';
                    }
                },

            }
        }
    };
    return Controller;
});
