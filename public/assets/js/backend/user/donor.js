define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/donor/index' + location.search,
                    add_url: 'user/donor/add',
                    edit_url: 'user/donor/edit',
                    del_url: 'user/donor/del',
                    multi_url: 'user/donor/multi',
                    import_url: 'user/donor/import',
                    table: 'user',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='lead_source']", form).addClass("selectpage").data("source", "lead_source/index").data("primaryKey", "name").data("field", "name").data("orderBy", "id asc");
                $("input[name='admin_id']", form).addClass("selectpage").data("source", "auth/admin/index").data("primaryKey", "id").data("field", "username").data("orderBy", "id asc");
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
                        {field: 'id', title: __('Id')},
                        {
                            field: 'username',
                            title: 'Name',
                            operate: false,
                            formatter: Controller.api.formatter.username
                        },
                        // {field: 'avatar', title: 'Avatar', operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'mobile', title: __('Phone'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {
                            field: 'progress',
                            title: 'Form grogress',
                            operate: false,
                            formatter: Controller.api.formatter.progress
                        },
                        {field: 'lead_source', title: __('Lead source'), operate: 'LIKE'},
                        {
                            field: 'admin',
                            title: 'Owner',
                            operate: false,
                            formatter: Controller.api.formatter.admin
                        },
                        {
                            field: 'admin_id',
                            title: 'Owner',
                            operate: 'LIKE',
                            addclass: 'selectpage',
                            visible: false
                        },
                        {field: 'surrogacy_progress', title:'Tag',operate:false,
                            formatter: function (value, row, index){
                                var surrogacy_progress = row.surrogacy_progress
                                if(surrogacy_progress){
                                    html ='<button class="btn btn-success btn-xs" style="margin-right: 5px;margin-bottom: 5px;">'+surrogacy_progress+'</button>';
                                    return html;
                                }else{
                                    html ='<button class="btn btn-del btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Null</button>';
                                    return html;
                                }
                            }
                        },
                        {field: 'follow_ups', title:'# of follow-ups',operate:'LIKE',formatter: function (value, row, index){
                                if(value==0){
                                    return "-";
                                }else{
                                    return value;
                                }
                            }
                        },
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange',
                            autocomplete:false
                            // formatter: Table.api.formatter.datetime
                        },
                        {field: 'pass_time', title: __('Review time'), operate:'RANGE', addclass:'datetimerange',
                            autocomplete:false,
                            visible:function (row) {
                                if (row.examine_status==1){
                                    return true;
                                }else {
                                    return false;
                                }
                            },
                        },
                        {
                            field: 'bio', title: __('Form grogress'), searchList: function (column) {
                                return Template('form_progresstpl', {});
                            }, visible: false
                        },
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {
                                    name: 'detail',
                                    text: __('Detail'),
                                    title: function (row) {
                                        return 'Review: '+ row.first_name+' '+ row.last_name;
                                    },
                                    classname: 'btn btn-xs btn-info btn-addtabs',
                                    url: 'user/donor/detail',
                                },
                                {
                                    name: 'Review',
                                    text: __('Review'),
                                    title: function (row) {
                                        return 'Review: '+ row.first_name+' '+ row.last_name;
                                    },
                                    classname: 'btn btn-xs btn-warning  btn-addtabs',
                                    url: 'user/donor/examine',
                                },
                                {name: 'Note',
                                    text: 'Note',
                                    title: function (row) {
                                        return 'Note: '+ row.first_name+' '+ row.last_name;
                                    },
                                    icon: 'fa fa-commenting-o',
                                    extend: 'data-area=\'["80%", "80%"]\'',
                                    // classname: 'btn btn-xs btn-danger  btn-dialog btn-de',
                                    classname: 'btn btn-xs btn-danger btn-addtabs',
                                    url: 'user/donor/notes'
                                },
                                {
                                    name: 'Word',
                                    text: __('Word'),
                                    title: __('Word'),
                                    classname: 'btn btn-xs btn-success  btn-ajax',
                                    url: 'user/donor/donor_word',
                                    success: function (data, ret) {
                                        var file =Config.url+ret.data;
                                        window.open(file);
                                    },
                                },
                                {
                                    name: 'del',
                                    icon: 'fa fa-trash',
                                    title: __('Del'),
                                    // visible:function (row) {
                                    //     if (row.examine_status==2){
                                    //         return true;
                                    //     }else {
                                    //         return false;
                                    //     }
                                    // },
                                    extend: 'data-toggle="tooltip"',
                                    classname: 'btn btn-xs btn-danger btn-delone'
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
                    filter.examine_status = '3';
                    op.examine_status = "=";
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                },
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

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

                    if (value) {
                        // delete filter.id;
                        //这里可以自定义多个筛选条件
                        filter.examine_status = value;
                    } else {
                        // delete filter.id;
                        //选全部时要移除相应的条件
                        delete filter.examine_status;
                    }

                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
            });


            $(document).on('click', '.btn-fenpei', function () {
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
                            url: "user/donor/fenpei",
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

            $(document).on("click", ".btn-reset-search", function() {
                location.reload();
            });

            $(document).on("click", ".btn-form-complete", function () {
                var options = table.bootstrapTable('getOptions');
                var queryParams = options.queryParams;
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    //这一行必须要存在,否则在点击下一页时会丢失搜索栏数据
                    params = queryParams(params);

                    //如果希望追加搜索条件,可使用
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};

                    // 示例:追加创建时间createtime搜索条件
                    filter.forms = 100; // 值
                    op.forms = '='; // 操作符，RANGE表示范围
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
                };
                table.bootstrapTable('refresh', {});
                Toastr.info("A surrogate who has completed all the search forms");
                return false;
            });

            $(document).on('click', '.btn-uploads', function () {
                var ids = Table.api.selectedids(table);
                Layer.open({
                    title: __('Classify'),
                    content: Template("uploadtpl", {}),
                    area:["600px","400px"],
                    btn: [__('OK')],
                    yes: function (index, layero) {
                        var file = $("input[name='file']", layero).val();
                        Fast.api.ajax({
                            url: "user/user/upload",
                            type: "post",
                            data: {file: file, ids: ids.join(',')},
                        }, function () {
                            table.bootstrapTable('refresh', {});
                            Layer.close(index);
                        });
                    },
                    success: function (layero, index) {
                    }
                });
                Controller.api.bindevent();
            });

            $(document).on('click', '.btn-progress', function () {
                var ids = Table.api.selectedids(table);
                Layer.open({
                    title: __('Surrogacy progress'),
                    content: Template("progresstpl", {}),
                    area:["600px","400px"],
                    btn: [__('OK')],
                    yes: function (index, layero) {
                        var progress = $("input[name='progress']", layero).val();
                        Fast.api.ajax({
                            url: "user/user/surrogacy_progress",
                            type: "post",
                            data: {progress: progress, ids: ids.join(',')},
                        }, function () {
                            table.bootstrapTable('refresh', {});
                            Layer.close(index);
                        });
                    },
                    success: function (layero, index) {
                    }
                });
                Controller.api.bindevent();
            });

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
                url: 'user/donor/recyclebin' + location.search,
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
                                    url: 'user/donor/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'user/donor/destroy',
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
        detail:function () {

            $(document).on("click", ".btn-examine", function () {
                var type = $(this).attr('data-type');
                var uid = $(this).attr('data-uid');
                // alert(type)
                layer.confirm('Are you sure you want to pass the Background check？', {
                    btn : [ 'Yes', 'No' ]//按钮
                }, function(index) {
                    $.ajax({
                        url:'user/donor/examine_status',
                        type:'GET',
                        dataType:"json",
                        data:{type,uid},
                        async:false,
                        success:function(res){
                            layer.close(index);
                            layer.msg('Examine success');
                            location.reload();
                        }
                    });

                });
            });

            $(document).on("click", ".btn-refuse", function () {
                var type = $(this).attr('data-type');
                var uid = $(this).attr('data-uid');
                var content = $(this).attr('data-content');
                var html = '';
                html += '<div>';
                html += '<div >Content:</div>';
                if (content){
                    html += '<textarea name="txt_remark" id="remark" placeholder="If the review fails, please fill in the reason" style="padding: 10px;width:400px;height:100px;">'+content+'</textarea>';
                }else{
                    html += '<textarea name="txt_remark" id="remark" placeholder="If the review fails, please fill in the reason" style="padding: 10px;width:400px;height:100px;"></textarea>';
                }

                html += ' </div>';
                layer.prompt({
                    formType : 0,
                    title : 'To Refuse',
                    content:html,
                    btn:['yes','no'],
                    yes: function (index, layero) {
                        var textarea = $('#remark').val();//获取多行文本框的值
                        if (!textarea){
                            layer.msg('If the review fails, please fill in the reason')
                            return false;
                        }
                        $.ajax({
                            url:'user/donor/refuse_status',
                            type:'GET',
                            dataType:"json",
                            data:{uid,type,textarea},
                            async:false,
                            success:function(res){
                                console.log(res);
                            }
                        });
                        layer.msg('Refuse success');
                        layer.close(index);
                        location.reload();
                    }
                });
            });

            Form.api.bindevent($("#background"));
            Controller.api.bindevent();
        },
        examine: function () {
            $(document).on("click", ".btn-examine", function () {
                var type = $(this).attr('data-type');
                var status = $(this).attr('data-status');
                var uid = $(this).attr('data-uid');
                if (status!='1'){
                    layer.msg('Not all forms filled out');
                    return false;
                }
                // alert(type)
                layer.confirm('Are you sure you want to pass the review？', {
                    btn : [ 'Yes', 'No' ]//按钮
                }, function(index) {
                    $.ajax({
                        url:'user/donor/examine_status',
                        type:'GET',
                        dataType:"json",
                        data:{type,uid},
                        async:false,
                        success:function(res){
                            layer.close(index);
                            layer.msg('Examine success');
                            // var i = type-1;
                            // $(".contact-tab-1").addClass('active')
                            // $(".contact-tab-1").addClass('show')
                            location.reload();
                        }
                    });

                });
            });

            $(document).on("click", ".btn-refuse", function () {
                var type = $(this).attr('data-type');
                var uid = $(this).attr('data-uid');
                var content = $(this).attr('data-content');
                var html = '';
                html += '<div>';
                html += '<div >Content:</div>';
                if (content){
                    html += '<textarea name="txt_remark" id="remark" placeholder="If the review fails, please fill in the reason" style="padding: 10px;width:400px;height:100px;">'+content+'</textarea>';
                }else{
                    html += '<textarea name="txt_remark" id="remark" placeholder="If the review fails, please fill in the reason" style="padding: 10px;width:400px;height:100px;"></textarea>';
                }

                html += ' </div>';
                layer.prompt({
                    formType : 0,
                    title : 'To Refuse',
                    content:html,
                    btn:['yes','no'],
                    yes: function (index, layero) {
                        var textarea = $('#remark').val();//获取多行文本框的值
                        if (!textarea){
                            layer.msg('If the review fails, please fill in the reason')
                            return false;
                        }
                        $.ajax({
                            url:'user/donor/refuse_status',
                            type:'GET',
                            dataType:"json",
                            data:{uid,type,textarea},
                            async:false,
                            success:function(res){
                                console.log(res);
                            }
                        });
                        layer.msg('Refuse success');
                        layer.close(index);
                        window.parent.location.reload();
                    }
                });
            });

            Controller.api.bindevent();
        },
        notes: function () {

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/donor/notes/ids/'+$('#ids').val() + location.search,
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
                        {field: 'id', title: __('Id')},
                        {field: 'content', title: __('Note'), operate: false,
                            formatter: function (value, row, index){
                                return '<textarea  cols="50" rows="2" disabled>'+value+'</textarea>'
                            }
                        },
                        {field: 'file', title:'File',operate:false,
                            formatter: function (value, row, index){
                                var file = row.file;
                                if(file){
                                    html ='<a  href="'+file+'" download="'+file+'" title="Click to download"><button class="btn btn-info btn-xs" style="margin-right: 5px;margin-bottom: 5px;">True</button></a>';
                                    return html;
                                }else{
                                    html ='<button class="btn btn-danger btn-xs" style="margin-right: 5px;margin-bottom: 5px;">False</button>';
                                    return html;
                                }
                            }
                        },
                        {field: 'admin.username', title: __('Created by')},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Pass'),"2":__('DQ'),"3":__('Pending Review')}, formatter: Table.api.formatter.status},
                        {field: 'contact_method', title: __('Contact method'), searchList: {"1":__('Phone'),"2":__('Email')}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,datetimeFormat:"MM-DD-YYYY  HH:mm:ss"},

                    ]
                ],

            });

            $('.btn-save').click(function () {
                var form_id = $('#form_id').val();
                var email = $('#email').val();
                var content = $('#record-content').val();
                if (!content){
                    layer.msg('Note cannot be empty');
                    return false;
                }
                var file = $('.thumbnail').attr('href');
                var status = $('#status option:selected').val();
                var contact_method = $('#contact_method option:selected').val();

                $.ajax({
                    url:'user/donor/notes_add',
                    type:'get',
                    dataType:"json",
                    data:{form_id,email,content,file,status,contact_method},
                    async:false,
                    success:function(res){
                        layer.msg(res.msg);
                        table.bootstrapTable('refresh', {});
                    }
                });
            })

            // 为表格绑定事件
            Table.api.bindevent(table);
            Form.api.bindevent($("#notes1"));
        },

        photos: function () {
            Controller.api.bindevent();
        },


        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                username: function (value, row, index) {
                    var first_name = row.first_name
                    var last_name = row.last_name
                    return first_name + ' '+last_name;
                },
                progress: function (value, row, index) {
                    // var progress = row.forms
                    // return '<button class="btn btn-success btn-xs">'+progress+'%'+'</button>'
                    var html = '';
                    var pre_screen = row.donor_prescreen.status;
                    var donor_personal = row.donor_personal.status;
                    var donor_photos = row.donor_photos.status;
                    var education = row.education.status;
                    var character = row.character.status;
                    var donor_medical = row.donor_medical.status;
                    var examine_file = row.examine_file;
                    if (pre_screen==1){
                        html +='<button class="btn btn-success btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Pre-screening</button>';
                    }

                    if (donor_personal==1){
                        html +='<button class="btn btn-warning btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Personal Information</button>';
                    }
                    if (donor_photos==1){
                        html +='<button class="btn btn-warning btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Photos</button>';
                    }
                    if (education==1){
                        html +='<div>';
                        html +='<button class="btn btn-danger btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Education</button>';
                    }
                    if (character==1){
                        html +='<button class="btn btn-success btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Character</button>';
                    }
                    if (donor_medical==1){
                        html +='<button class="btn btn-info btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Medical Information</button>';
                        html +='</div>';
                    }

                    if (examine_file==1){
                        html +='<button class="btn btn-warning btn-xs" style="margin-right: 5px;margin-bottom: 5px;">Simple process</button>'
                    }
                    return html;
                },
                admin: function (value, row, index) {
                    var username = row.admin.username;
                    if (username){
                        return username ;
                    }else {
                        return '-';
                    }
                },

            }
        }
    };
    return Controller;
});
