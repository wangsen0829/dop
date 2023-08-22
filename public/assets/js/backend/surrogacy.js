define(['jquery', 'bootstrap', 'backend', 'table', 'form','jSignature'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        pre_screen: function () {
            Controller.api.bindevent();
        },
        personal_info: function () {
            Controller.api.bindevent();
        },
        obstetric_history: function () {
            Controller.api.bindevent();
        },
        medical_information: function () {
            Controller.api.bindevent();
        },
        about_surrogacy: function () {
            Controller.api.bindevent();
        },
        other_information: function () {
            Controller.api.bindevent();
        },
        surrogate_photo: function () {
            Controller.api.bindevent();
        },
        additional_information: function () {
            Controller.api.bindevent();
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
            Controller.api.bindevent();
        },
        background: function () {
            Controller.api.bindevent();
        },
        sbp: function () {
            Controller.api.bindevent();
        },
        medical_fax: function () {
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
                            Toastr.info("Successfully generated Word document");
                            $(this).text('Fax generated')
                            window.location.href = 'https://vip.dopusa.com/'+res.data;
                        }else{
                            Toastr.info('Failed to generate Word document')
                        }
                    }
                })
            })
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
