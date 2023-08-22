define(['jquery', 'bootstrap', 'frontend', 'form', 'template','jSignature'], function ($, undefined, Frontend, Form, Template) {
    var validatoroptions = {
        invalid: function (form, errors) {
            $.each(errors, function (i, j) {
                Layer.msg(j);
            });
        }
    };
    var Controller = {
        login: function () {

            //本地验证未通过时提示
            $("#login-form").data("validator-options", validatoroptions);

            //为表单绑定事件
            Form.api.bindevent($("#login-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = ret.url ? ret.url : "/";
                }, 1000);
            });

            //忘记密码
            $(document).on("click", ".btn-forgot", function () {
                var id = "resetpwdtpl";
                var content = Template(id, {});
                Layer.open({
                    type: 1,
                    title: __('Reset password'),
                    area: ["450px", "355px"],
                    content: content,
                    success: function (layero) {
                        var rule = $("#resetpwd-form input[name='captcha']").data("rule");
                        Form.api.bindevent($("#resetpwd-form", layero), function (data) {
                            Layer.closeAll();
                        });
                        $(layero).on("change", "input[name=type]", function () {
                            var type = $(this).val();
                            $("div.form-group[data-type]").addClass("hide");
                            $("div.form-group[data-type='" + type + "']").removeClass("hide");
                            $('#resetpwd-form').validator("setField", {
                                captcha: rule.replace(/remote\((.*)\)/, "remote(" + $(this).data("check-url") + ", event=resetpwd, " + type + ":#" + type + ")")
                            });
                            $(".btn-captcha").data("url", $(this).data("send-url")).data("type", type);
                        });
                    }
                });
            });
        },
        register: function () {
            //本地验证未通过时提示
            $("#register-form").data("validator-options", validatoroptions);

            //为表单绑定事件
            Form.api.bindevent($("#register-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = ret.url ? ret.url : "/";
                }, 1000);
            }, function (data) {
                $("input[name=captcha]").next(".input-group-btn").find("img").trigger("click");
            });
        },
        changepwd: function () {
            //本地验证未通过时提示
            $("#changepwd-form").data("validator-options", validatoroptions);

            //为表单绑定事件
            Form.api.bindevent($("#changepwd-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = ret.url ? ret.url : "/";
                }, 1000);
            });
        },
        profile: function () {
            // 给上传按钮添加上传成功事件
            $("#faupload-avatar").data("upload-success", function (data) {
                var url = Fast.api.cdnurl(data.url);
                $(".profile-user-img").prop("src", url);
                Toastr.success(__('Uploaded successful'));
            });
            Form.api.bindevent($("#profile-form"));
            $(document).on("click", ".btn-change", function () {
                var that = this;
                var id = $(this).data("type") + "tpl";
                var content = Template(id, {});
                Layer.open({
                    type: 1,
                    title: "修改",
                    area: ["400px", "250px"],
                    content: content,
                    success: function (layero) {
                        var form = $("form", layero);
                        Form.api.bindevent(form, function (data) {
                            location.reload();
                            Layer.closeAll();
                        });
                    }
                });
            });
        },
        edit_profile :function () {
            Form.api.bindevent($("#edit_profile"));
        },
        attachment: function () {
            require(['table'], function (Table) {

                // 初始化表格参数配置
                Table.api.init({
                    extend: {
                        index_url: 'user/attachment',
                    }
                });
                var urlArr = [];
                var multiple = Fast.api.query('multiple');
                multiple = multiple == 'true' ? true : false;

                var table = $("#table");

                table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function (e, row) {
                    if (e.type == 'check' || e.type == 'uncheck') {
                        row = [row];
                    } else {
                        urlArr = [];
                    }
                    $.each(row, function (i, j) {
                        if (e.type.indexOf("uncheck") > -1) {
                            var index = urlArr.indexOf(j.url);
                            if (index > -1) {
                                urlArr.splice(index, 1);
                            }
                        } else {
                            urlArr.indexOf(j.url) == -1 && urlArr.push(j.url);
                        }
                    });
                });

                // 初始化表格
                table.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    sortName: 'id',
                    showToggle: false,
                    showExport: false,
                    fixedColumns: true,
                    fixedRightNumber: 1,
                    columns: [
                        [
                            {field: 'state', checkbox: multiple, visible: multiple, operate: false},
                            {field: 'id', title: __('Id'), operate: false},
                            {
                                field: 'url', title: __('Preview'), formatter: function (value, row, index) {
                                    var html = '';
                                    if (row.mimetype.indexOf("image") > -1) {
                                        html = '<a href="' + row.fullurl + '" target="_blank"><img src="' + row.fullurl + row.thumb_style + '" alt="" style="max-height:60px;max-width:120px"></a>';
                                    } else {
                                        html = '<a href="' + row.fullurl + '" target="_blank"><img src="' + Fast.api.fixurl("ajax/icon") + "?suffix=" + row.imagetype + '" alt="" style="max-height:90px;max-width:120px"></a>';
                                    }
                                    return '<div style="width:120px;margin:0 auto;text-align:center;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">' + html + '</div>';
                                }
                            },
                            {
                                field: 'filename', title: __('Filename'), formatter: function (value, row, index) {
                                    return '<div style="width:150px;margin:0 auto;text-align:center;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">' + Table.api.formatter.search.call(this, value, row, index) + '</div>';
                                }, operate: 'like'
                            },
                            {field: 'imagewidth', title: __('Imagewidth'), operate: false},
                            {field: 'imageheight', title: __('Imageheight'), operate: false},
                            {field: 'mimetype', title: __('Mimetype'), formatter: Table.api.formatter.search},
                            {field: 'createtime', title: __('Createtime'), width: 120, formatter: Table.api.formatter.datetime, datetimeFormat: 'YYYY-MM-DD', operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                            {
                                field: 'operate', title: __('Operate'), width: 85, events: {
                                    'click .btn-chooseone': function (e, value, row, index) {
                                        Fast.api.close({url: row.url, multiple: multiple});
                                    },
                                }, formatter: function () {
                                    return '<a href="javascript:;" class="btn btn-danger btn-chooseone btn-xs"><i class="fa fa-check"></i> ' + __('Choose') + '</a>';
                                }
                            }
                        ]
                    ]
                });

                // 选中多个
                $(document).on("click", ".btn-choose-multi", function () {
                    Fast.api.close({url: urlArr.join(","), multiple: multiple});
                });

                // 为表格绑定事件
                Table.api.bindevent(table);
                require(['upload'], function (Upload) {
                    Upload.api.upload($("#toolbar .faupload"), function () {
                        $(".btn-refresh").trigger("click");
                    });
                });

            });
        },
        obstetric_history: function () {
            Form.api.bindevent($("#obstetric_history"));
        },
        medical_information: function () {
            Form.api.bindevent($("#medical_information"));
        },
        surrogate_photo: function () {
            Form.api.bindevent($("#surrogate_photo"));
        },
        additional_information: function () {
            Form.api.bindevent($("#additional_information"));
        },
        health_record_release: function () {
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
            Form.api.bindevent($("#health_record_release"));
        },

        forgot_password: function () {
            $('.ws_password').click(function () {
                var email = $('.email').val();
                if (!email){
                    alert('Email cannot be empty');
                    return false;
                }else{
                    //  验证邮箱的正则表达式
                    const regEmail = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/
                    if (regEmail.test(email)) {

                        $.ajax({
                            url:'user/pass_email',
                            type:'POST',
                            data:{email},
                            async:false,
                            success:function(res){
                               if (res.code==1){
                                   layer.msg('Successfully sent email');

                                   setTimeout(function() {
                                            window.location.href = "https://vip.dopusa.com/index/user/login.html";
                                       },
                                       2000);
                               }else{
                                   layer.msg('Error, please contact the administrator')
                               }
                            }
                        });
                    }else{
                        alert('Incorrect email format');
                        return false;
                    }



                }

            })
            Form.api.bindevent($("#pass-form"));
        },
    };
    return Controller;
});
