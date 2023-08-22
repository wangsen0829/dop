define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Echarts) {

    var Controller = {
        index: function () {

            //数据渲染
            var myChart = [],option = [];
            for(var i = 0,len = Config.totalChart.length; i < len; i++) {
                //标题与副标题
                let _tit = {
                    text: Config.totalChart[i].title,
                    subtext: Config.totalChart[i].subtext
                };

                if(Config.totalChart[i].chart_type == 'pie'){
                    let series_data = Config.totalChart[i].data;
                    option[i] = {
                        title: _tit,
                        tooltip: {
                            trigger: 'item',
                            formatter: (params) => {
                                return params.name + ' : ' + params.value + ' ' + series_data[params.dataIndex].unit + '（' + params.percent + '%）';
                            }
                        },
                        legend: {
                            type: 'scroll',      //可滚动翻页，当图例数量较多时使用
                            orient: 'horizontal',//vertical=垂直显示,horizontal=水平显示
                            x:'center',          //left/center/right
                            y:'bottom',          //top/center/bottom
                            padding: [10,0,0,0],
                            show: true,
                            data: Config.totalChart[i].category
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                dataView: {show: true, readOnly: false},
                                saveAsImage: {show: true}
                            }
                        },
                        series: [
                            {
                                name: Config.totalChart[i].legend_title,
                                type: 'pie',
                                radius: ['20%', '65%'],
                                avoidLabelOverlap: true,//防止标签重叠
                                stillShowZeroSum:  true,//为0的是否显示
                                normal: {
                                    show: false,//是否显示标签
                                },
                                data: series_data
                            }
                        ]
                    };
                } else if (Config.totalChart[i].chart_type == 'graph') {
                    let series_data = Config.totalChart[i].series;
                    option[i] = {
                        title: _tit,
                        tooltip: {
                            trigger: 'axis',
                            formatter: (params) => {
                                let relVal = params[0].name;
                                for (let fi = 0, fl = params.length; fi < fl; fi++) {
                                    relVal = relVal + '<br/>' + params[fi].marker + params[fi].seriesName + ' : ' + params[fi].data + ' ' + series_data[params[fi].seriesIndex].unit;
                                }
                                return relVal;
                            }
                        },
                        legend: {
                            type: 'scroll',      //可滚动翻页，当图例数量较多时使用
                            orient: 'horizontal',//vertical=垂直显示,horizontal=水平显示
                            x:'center',          //left/center/right
                            y:'bottom',          //top/center/bottom
                            padding: [10,0,0,0],
                            show: true,
                            data: Config.totalChart[i].legend_title
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                dataView: {show: true, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        calculable: true,
                        grid: [{
                            top: 65
                        }],
                        xAxis: [{
                            type: 'category',
                            data: Config.totalChart[i].category
                        }],
                        yAxis: [{
                            type: 'value'
                        }],
                        series: series_data
                    };
                } else {
                    let series_data = Config.totalChart[i].series;
                    option[i] = {
                        title: _tit,
                        tooltip: {
                            trigger: 'axis',
                            formatter: (params) => {
                                let relVal = params[0].name;
                                for (let fi = 0, fl = params.length; fi < fl; fi++) {
                                    relVal = relVal + '<br/>' + params[fi].marker + params[fi].seriesName + ' : ' + params[fi].data + ' ' + series_data[params[fi].seriesIndex].unit;
                                }
                                return relVal;
                            }
                        },
                        legend: {
                            type: 'scroll',      //可滚动翻页，当图例数量较多时使用
                            orient: 'horizontal',//vertical=垂直显示,horizontal=水平显示
                            x:'center',          //left/center/right
                            y:'bottom',          //top/center/bottom
                            padding: [10,0,0,0],
                            show: true,
                            data: Config.totalChart[i].legend_title
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                dataView: {show: true, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: true},
                                saveAsImage: {show: true}
                            }
                        },
                        calculable: true,
                        grid: [{
                            top: 65
                        }],
                        xAxis: [{
                            type: 'category',
                            data: Config.totalChart[i].category
                        }],
                        yAxis: [{
                            type: 'value'
                        }],
                        series: series_data
                    };
                }

                myChart[i] = Echarts.init($('#echarts' + Config.totalChart[i].id)[0], 'walden');
                myChart[i].setOption(option[i]);
            }

            //窗口大小改变
            $(window).resize(function () {
                for(var i = 0,len = Config.totalChart.length; i < len; i++) {
                    myChart[i].resize();
                }
            });

            //选项卡切入事件
            $(document).on("click", "#resetecharts", function () {
                setTimeout(function () {
                    $(window).trigger("resize");
                }, 50);
            });

            //绑定搜索表单
            Form.api.bindevent($("#form1"));

            //自定义时间范围
            $("#customcharts_datetimerange").data("callback", function (start, end) {
                var date = start.format(this.locale.format) + " - " + end.format(this.locale.format);
                $(this.element).val(date);
                refresh_echart(date);
            });

            //重新查询数据
            var refresh_echart = function (date) {
                Fast.api.ajax({
                    url: 'customcharts/show/index',
                    data: {date: date},
                    loading: false
                }, function (data) {
                    for(j = 0,len = data.length; j < len; j++) {
                        if (data[j].chart_type == 'pie') {
                            if (option[j].legend != undefined) {
                                option[j].legend.data = data[j].category;
                            }
                            if (option[j].xAxis != undefined) {
                                option[j].xAxis[0].data = data[j].category;
                            }
                            option[j].series[0].data = data[j].data;
                        } else {
                            if (option[j].legend != undefined) {
                                option[j].legend.data = data[j].legend_title;
                            }
                            if (option[j].xAxis != undefined) {
                                option[j].xAxis[0].data = data[j].category;
                            }
                            option[j].series = data[j].series;
                        }
                        myChart[j].clear();
                        myChart[j].setOption(option[j], true);
                    }
                    return false;
                });
            };

            //点击按钮
            $(document).on("click", ".btn-filter", function () {
                var label = $(this).text();
                var obj = $(this).closest("form").find("#customcharts_datetimerange").data("daterangepicker");
                var dates = obj.ranges[label];
                obj.startDate = dates[0];
                obj.endDate = dates[1];
                obj.clickApply();
            });

            //点击刷新
            $(document).on("click", ".btn-refresh", function () {
                var date = $('#customcharts_datetimerange').val();
                refresh_echart(date);
            });

            //每隔一分钟定时刷新图表
            setInterval(function () {
                $(".btn-refresh").trigger("click");
            }, 60000);

        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
