define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'Fenpei/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
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
            $(function () {
                let selectAll = $('#selectAll')
                let ckboxs = $("#table").find("input[type=checkbox]");
                console.log(ckboxs.length);
                selectAll.onclick=function(){
                    //  全选按钮的状态
                    let state = selectAll.checked;
                    //循环每一个ckboxs，为全选状态
                    for(let i=0;i<ckboxs.length;i++){
                        ckboxs[i].checked=state;
                    }
                }
            })

            //获取选中项
            $(document).on("click", ".btn-selected", function () {
                //在templateView的模式下不能调用table.bootstrapTable('getSelections')来获取选中的ID,只能通过下面的Table.api.selectedids来获取
                Layer.alert(JSON.stringify(Table.api.selectedids(table)));
            });

            $(document).on("click", ".distribution", function () {
                var parents = $('#parents').val();
                if (parents =='0'){
                    layer.msg("请先选择准父母");
                    return false;
                }
                var ids = Table.api.selectedids(table);//获取选中列的id
                alert(ids);
            });

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