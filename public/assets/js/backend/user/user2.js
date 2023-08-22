define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: '',
                    edit_url: '',
                    del_url: 'user/user/del',
                    multi_url: '',
                }
            });

            var table = $("#table");

            Template.helper("Moment", Moment);

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                templateView: true,
                //禁用默认搜索
                search: true,
                //启用普通表单搜索
                commonSearch: true,
                //可以控制是否默认显示搜索单表,false则隐藏,默认为false
                searchFormVisible: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        {field: 'first_name', title: 'First name', operate: 'LIKE'},
                        {field: 'last_name', title: 'Last name', operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                    ],
                ],
                //分页大小
                pageSize: 6,


            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on("click", ".btn-reset-search", function() {
                // $(".form-commonsearch [type=reset]").trigger("click");
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


            //获取选中项
            $(document).on("click", ".btn-selected", function () {
                //在templateView的模式下不能调用table.bootstrapTable('getSelections')来获取选中的ID,只能通过下面的Table.api.selectedids来获取
                Layer.alert(JSON.stringify(Table.api.selectedids(table)));
            });

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
        },

        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },

        detail:function () {
            $(document).on("click", ".ws-fax", function () {
                var id = $(this).attr('data-id');
                var uid = $(this).attr('data-uid');
                var type = $(this).attr('data-type');
                $.ajax({
                    url:'user/user/word',
                    type:'GET',
                    dataType:"json",
                    data:{id,uid,type},
                    async:false,
                    success:function(res){
                        // alert(res)
                        if (res.code == 1){
                            layer.msg('生成word文档成功')
                            $(this).text('已生成Fax')
                            window.location.href = 'http://dop.ifcivf.cn/'+res.data;
                        }else{
                            layer.msg('生成word文档失败')
                        }
                    }
                })
            })

            Form.api.bindevent($("#background"));
            Controller.api.bindevent();
        },
        examine: function () {
            $(document).on("click", ".btn-examine", function () {
                var type = $(this).attr('data-type');
                var uid = $(this).attr('data-uid');
                // alert(type)
                layer.confirm('Are you sure you want to pass the review？', {
                    btn : [ 'Yes', 'No' ]//按钮
                }, function(index) {
                    $.ajax({
                        url:'user/user/examine_status',
                        type:'GET',
                        dataType:"json",
                        data:{type,uid},
                        async:false,
                        success:function(res){
                            layer.close(index);
                            layer.msg('Examine success');
                            // window.parent.location.reload();
                        }
                    });

                });
            });
            // $(document).on("click", ".btn-refuse", function () {
            //     var type = $(this).attr('data-type');
            //     var uid = $(this).attr('data-uid');
            //     var textarea = $(this).prev().prev().val();
            //     if (textarea.length==0){
            //         layer.msg('If the review fails, please fill in the reason');
            //         return false;
            //     }
            //
            //     layer.confirm('Are you sure you want to pass the review？', {
            //         btn : [ 'Yes', 'No' ]//按钮
            //     }, function(index) {
            //         $.ajax({
            //             url:'user/user/refuse_status',
            //             type:'GET',
            //             dataType:"json",
            //             data:{type,uid,textarea},
            //             async:false,
            //             success:function(res){
            //                 layer.close(index);
            //                 layer.msg('successful');
            //                 // Toastr.info(" successful");
            //             }
            //         });
            //
            //     });
            // });
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
                            url:'user/user/refuse_status',
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

            $(document).ready(function () {
                var index = 0;
                var images = [];
                var tag_name = '';
                // console.log("初始化", images);
                //初始化签名样式
                var arguments = {
                    width: '800px',
                    height: '400px',
                    cssclass: 'signature',
                    signatureLine: false,//去除默认画布上那条横线
                    lineWidth: '5',
                };
                $("#signature").jSignature(arguments);


                //清除重写
                $('#clearCanvas').on('click', function () {
                    $("#signature").jSignature("reset");
                    //清除重写调用方式
                    // $('#signName').clearSignature();
                    $('.popupBox').css('display', 'none');

                });
                //点击唤起签名
                $('.signClass').on('click', function (e) {
                    tag_name = e.currentTarget.dataset.tag_name;//每个签名的标志
                    index = $(this).index();
                    $('.popupBox').css('display', 'flex');
                    $('.popupBox').css('z-index', '999');
                });
                //确认按钮
                $('#createImage').on('click', function () {
                    // console.log(tag_name)
                    //标准格式但是base64会被tp框架过滤，所不校验，但是jSignature默认是使用png
                    var datapair = $("#signature").jSignature("getData", "image");
                    var i = new Image();
                    i.src = "data:" + datapair[0] + "," + datapair[1];
                    i.image = datapair[1];
                    $.ajax({
                        url: "/addons/treaty/index/upload",
                        data: {'image_data': encodeURIComponent(i.src)},
                        type: "post",
                        success: function (res) {
                            if (res.code == 1) {
                                layer.msg(res.msg);
                                // console.log("tag_name",tag_name);
                                // console.log("开始",images);
                                var is_replace = 0;
                                $.each(images, function (index, item) {
                                    if (item['name'] == tag_name) {
                                        images[index] = {name: tag_name, url: res.data.url};
                                        is_replace = 1;
                                    }
                                });
                                if (!is_replace) {
                                    images.push({name: tag_name, url: res.data.url});
                                }
                                $('.signature_img').remove();
                                // console.log("结束",images);
                                $('#newImage').attr('src', res.data.url);
                                $('#canvas').val(res.data.url);
                                var className = ".sign_" + tag_name;
                                $(className).html(' <img  class="signature_img" src="' + res.data.url + '" />')
                                $("#signature").jSignature("reset");
                                $('.popupBox').css('display', 'none');
                            } else {
                                layer.msg(res.msg);
                            }
                        }
                    });
                });


                $(".btn-submit").click(function () {
                    var category_id = $("#category_id").val();
                    var token = $("input[name='__token__']").val();
                    $.ajax({
                        url: "/addons/treaty/index/index",
                        data: {__token__: token, images: images, category_id: category_id},
                        type: "post",
                        success: function (res) {
                            if (res.code == 1) {
                                layer.msg(res.msg);
                                setTimeout(function () {
                                    window.location.href = res.url;
                                }, 1000)

                            } else {
                                $("input[name='__token__']").val(res.data.token)
                                layer.msg(res.msg);
                                return false;
                            }
                        }
                    })
                });

            });

            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },

            formatter: {

            },
            events: {

            },
        },

    };
    return Controller;
});