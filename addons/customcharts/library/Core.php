<?php

namespace addons\customcharts\library;

use think\Db;

class Core
{
    /**
     * 数量统计
     * \addons\customcharts\library\Core::totalNumber();
     * @author Created by Xing <464401240@qq.com>
     */
    public static function totalNumber()
    {
        $result = [];
        $model = new \app\admin\model\customcharts\Totalnumber();
        $list = $model->order('weigh ASC')->select();
        $typeTimeList = $model->getTypeTimeList();

        foreach ($list as $row) {
            //初始化
            $result[$row->id] = [
                'icon_color' => $row->icon_color,
                'is_money' => $row->is_money,
                'total' => 0,
                'lastTotal' => 0,
                'ratio' => 0, //不等于0页面上才会显示
                'title' => $row->title,
                'icon' => $row->icon,
                'subtitle' => __($typeTimeList[$row->type_time])
            ];

            //和
            if ($row->type_total == 'sum') {
                if ($row->type_time == 'all') {
                    $result[$row->id]['total'] = round(Db::table($row->name)
                        ->where($row->where)
                        ->sum($row->field_total), 2);
                } else {
                    //本期
                    $result[$row->id]['total'] = round(Db::table($row->name)
                        ->where($row->where)
                        ->whereTime($row->field_time, $row->type_time)
                        ->sum($row->field_total), 2);

                    //上期
                    if ($row->type_time == 'today') {
                        $lastTotal = round(Db::table($row->name)->where($row->where)
                            ->whereTime($row->field_time, 'yesterday')
                            ->sum($row->field_total), 2);
                    } else {
                        $lastTotal = round(Db::table($row->name)->where($row->where)
                            ->whereTime($row->field_time, 'last ' . $row->type_time)
                            ->sum($row->field_total), 2);
                    }
                }
            } else {

                //行
                if ($row->type_time == 'all') {
                    $result[$row->id]['total'] = round(Db::table($row->name)
                        ->where($row->where)
                        ->count($row->field_total), 2);
                } else {
                    //本期
                    $result[$row->id]['total'] = round(Db::table($row->name)
                        ->where($row->where)
                        ->whereTime($row->field_time, $row->type_time)
                        ->count($row->field_total), 2);

                    //上期
                    if ($row->type_time == 'today') {
                        $lastTotal = round(Db::table($row->name)->where($row->where)
                            ->whereTime($row->field_time, 'yesterday')
                            ->count($row->field_total), 2);
                    } else {
                        $lastTotal = round(Db::table($row->name)->where($row->where)
                            ->whereTime($row->field_time, 'last ' . $row->type_time)
                            ->count($row->field_total), 2);
                    }
                }
            }

            if (isset($lastTotal)) {
                $result[$row->id]['lastTotal'] = $lastTotal;
                //同比增长
                if ($lastTotal > 0) {
                    $result[$row->id]['ratio'] = ceil((($result[$row->id]['total'] - $lastTotal) / $lastTotal) * 100);
                } else {
                    $result[$row->id]['ratio'] = ($result[$row->id]['total'] > 0 ? 100 : 0);
                }
            }
            unset($lastTotal);
        }
        return $result;
    }

    /**
     * 图表统计
     * \addons\customcharts\library\Core::totalChart();
     * @author Created by Xing <464401240@qq.com>
     */
    public static function totalChart($date = '')
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {

        }
        if ($date) {
            list($start, $end) = explode(' - ', $date);
            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', -29, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $result = [];
        $list = \app\admin\model\customcharts\Chart::order('weigh ASC')->select();
        foreach ($list as $key => $row) {
            if ($row->chart_type == 'pie') {
                //饼状统计
                if ($row->group_field) {
                    //有分组
                    $result[$key] = self::totalChartPie($row, $starttime, $endtime);
                } else {
                    $result[$key] = self::totalChartPieNotGroup($row, $starttime, $endtime);
                }
            } else {
                //图表统计数据
                if ($row->group_field) {
                    //有分组
                    $result[$key] = self::totalChartGraph($row, $starttime, $endtime);
                } else {
                    $result[$key] = self::totalChartGraphNotGroup($row, $starttime, $endtime);
                }
            }
        }
        return self::dataMerge($result);
    }

    /**
     * 排行统计
     * \addons\customcharts\library\Core::totalRanking();
     * @author Created by Xing <464401240@qq.com>
     */
    public static function totalRanking()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {

        }
        $result = [];
        $list = \app\admin\model\customcharts\Ranking::order('weigh ASC')->select();
        foreach ($list as $row) {
            $row->show_num = $row->show_num < 1 ? 1 : intval($row->show_num);
            if ($row->type_total == 'sum') {
                //按和统计
                $result[$row->id] = self::totalRankingSum($row);
            } else {
                //按行统计
                $result[$row->id] = self::totalRankingCount($row);
            }

            //数据不足时不空，以保障页面样式对齐
            $datalen = count($result[$row->id]['data']);
            for ($i = 0; $i < ($row->show_num - $datalen); $i++) {
                $result[$row->id]['data'][] = [
                    'name'=> '-',
                    'nums'=> '-',
                    'ratio'=> '0'
                ];
            }
        }
        return $result;
    }

    /**
     * 饼状图统计
     * @author Created by Xing <464401240@qq.com>
     */
    private static function totalChartPie($row, $starttime, $endtime)
    {
        if ($row->type_total == 'sum') {
            //按和统计
            if ($row->join_table) {
                $data = Db::table($row->name)
                    ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                    ->where($row->where)
                    ->where($row->name . '.' . $row->field_time, 'between time', [$starttime, $endtime])
                    ->field("{$row->join_table}.{$row->field_show} as field_show,{$row->name}.{$row->group_field},SUM({$row->is_distinct} {$row->name}.{$row->field_total}) as nums")
                    ->group("{$row->name}.{$row->group_field}")
                    ->select();
            } else {
                $data = Db::table($row->name)
                    ->where($row->where)
                    ->where($row->field_time, 'between time', [$starttime, $endtime])
                    ->field("{$row->group_field},SUM({$row->is_distinct} {$row->field_total}) as nums")
                    ->group($row->group_field)
                    ->select();
            }
        } else {
            //按行统计
            if ($row->join_table) {
                $data = Db::table($row->name)
                    ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                    ->where($row->where)
                    ->where($row->name . '.' . $row->field_time, 'between time', [$starttime, $endtime])
                    ->field("{$row->join_table}.{$row->field_show} as field_show,{$row->name}.{$row->group_field},COUNT({$row->is_distinct} {$row->name}.{$row->field_total}) as nums")
                    ->group("{$row->name}.{$row->group_field}")
                    ->select();
            } else {
                $data = Db::table($row->name)
                    ->where($row->where)
                    ->where($row->field_time, 'between time', [$starttime, $endtime])
                    ->field("{$row->group_field},COUNT({$row->is_distinct} {$row->field_total}) as nums")
                    ->group($row->group_field)
                    ->select();
            }
        }
        //组装数据
        $result = [
            'category' => [],
            'data' => [],
            'id' => $row->id,
            'legend_title' => $row->legend_title,
            'unit' => $row->unit,
            'chart_type' => $row->chart_type,
            'title' => $row->title,
            'subtext' => $row->subtext
        ];

        $legend_str = '';
        foreach ($data as $index => $item) {
            if ($row->join_table) {
                $name = $item['field_show'];
            } else {
                $dictionary = json_decode($row->dictionary, true);
                $name = $dictionary[$item[$row->group_field]] ?? $row->group_field . '(' . $item[$row->group_field] . ')';
            }
            $result['category'][] = $name;
            $result['data'][] = ['value' => $item['nums'], 'name' => $name, 'unit' => $row->unit];
            $legend_str .= $name;
        }
        return $result;
    }

    /**
     * 饼状图统计-无分组
     * @author Created by Xing <464401240@qq.com>
     */
    private static function totalChartPieNotGroup($row, $starttime, $endtime)
    {
        if ($row->type_total == 'sum') {
            //按和统计
            $data = Db::table($row->name)
                ->where($row->where)
                ->where($row->field_time, 'between time', [$starttime, $endtime])
                ->field("SUM({$row->is_distinct} {$row->field_total}) as nums")
                ->select();
        } else {
            //按行统计
            $data = Db::table($row->name)
                ->where($row->where)
                ->where($row->field_time, 'between time', [$starttime, $endtime])
                ->field("COUNT({$row->is_distinct} {$row->field_total}) as nums")
                ->select();
        }

        //组装数据
        $result = [
            'category' => [],
            'data' => [],
            'id' => $row->id,
            'legend_title' => $row->legend_title,
            'unit' => $row->unit,
            'chart_type' => $row->chart_type,
            'title' => $row->title,
            'subtext' => $row->subtext
        ];
        foreach ($data as $index => $item) {
            $result['category'][] = $row->legend_title;
            $result['data'][] = ['value' => $item['nums'], 'name' => $row->legend_title, 'unit' => $row->unit];
        }

        return $result;
    }

    /**
     * 柱状图和曲线图统计
     * @author Created by Xing <464401240@qq.com>
     */
    private static function totalChartGraph($row, $starttime, $endtime)
    {
        $totalseconds = $endtime - $starttime;
        if ($totalseconds > 86400 * 30 * 2) {
            $format = '%Y-%m';
        } else {
            if ($totalseconds > 86400) {
                $format = '%Y-%m-%d';
            } else {
                $format = '%H:00';
            }
        }

        if ($row->type_total == 'sum') {
            if ($row->field_time_type == 'int' || $row->field_time_type == 'bigint') {
                $sql = $row->name . '.' . $row->group_field . ',SUM(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(FROM_UNIXTIME(' . $row->name . '.' . $row->field_time . '), "' . $format . '") AS field_time';
            } else {
                $sql = $row->name . '.' . $row->group_field . ',SUM(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(' . $row->name . '.' . $row->field_time . ', "' . $format . '") AS field_time';
            }
        } else {
            if ($row->field_time_type == 'int' || $row->field_time_type == 'bigint') {
                $sql = $row->name . '.' . $row->group_field . ',COUNT(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(FROM_UNIXTIME(' . $row->name . '.' . $row->field_time . '), "' . $format . '") AS field_time';
            } else {
                $sql = $row->name . '.' . $row->group_field . ',COUNT(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(' . $row->name . '.' . $row->field_time . ', "' . $format . '") AS field_time';
            }
        }

        if ($row->join_table) {
            $data = Db::table($row->name)
                ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                ->where($row->where)
                ->where($row->name . '.' . $row->field_time, 'between time', [$starttime, $endtime])
                ->field("{$row->join_table}.{$row->field_show} as field_show,{$sql}")
                ->group("field_time,{$row->name}.{$row->group_field}")
                ->select();
        } else {
            $data = Db::table($row->name)
                ->where($row->where)
                ->where($row->field_time, 'between time', [$starttime, $endtime])
                ->field("{$sql}")
                ->group('field_time,' . $row->group_field)
                ->select();
        }


        if ($totalseconds > 84600 * 30 * 2) {
            $starttime = strtotime('last month', $starttime);
            while (($starttime = strtotime('next month', $starttime)) <= $endtime) {
                $column[] = date('Y-m', $starttime);
            }
        } else {
            if ($totalseconds > 86400) {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("Y-m-d", $time);
                    $time += 86400;
                }
            } else {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("H:00", $time);
                    $time += 3600;
                }
            }
        }
        $datalist = array_fill_keys($column, 0);

        $series = [];
        $names = [];
        foreach ($data as $k => $v) {
            if (!isset($series[$v[$row->group_field]])) {
                $series[$v[$row->group_field]] = $datalist;
            }
            $series[$v[$row->group_field]][$v['field_time']] = round($v['amount'], 2);
            if ($row->join_table) {
                $name = $v['field_show'];
            } else {
                $dictionary = json_decode($row->dictionary, true);
                $name = $dictionary[$v[$row->group_field]] ?? $row->group_field . '(' . $v[$row->group_field] . ')';
            }
            $names[$v[$row->group_field]] = $name;
        }

        foreach ($datalist as $tim => $v) {
            foreach ($series as &$item) {
                if (!isset($item[$tim])) {
                    $item[$tim] = 0;
                }
            }
            unset($item);
        }

        //初始化结果数组
        $result = [
            'category' => [],
            'data' => [],
            'id' => $row->id,
            'legend_title' => [],
            'unit' => $row->unit,
            'chart_type' => $row->chart_type,
            'title' => $row->title,
            'subtext' => $row->subtext
        ];

        $legend_str = '';
        foreach ($series as $id => $item) {
            $name = $names[$id] ?? $row->group_field . '(' . $id . ')';
            $result['legend_title'][] = $name;
            $result['series'][] = [
                'name' => $names[$id] ?? $row->group_field . '(' . $id . ')',
                'type' => $row->chart_type == 'graph' ? 'line' : 'bar',
                'unit' => $row->unit,
                'data' => array_values($item),
                'markPoint' => [
                    'data' => [
                        ['type' => 'max', 'name' => '最大值'],
                        ['type' => 'min', 'name' => '最小值']
                    ]
                ],
                'markLine' => [
                    'data' => [
                        ['type' => 'average', 'name' => '平均值']
                    ]
                ]
            ];
            $legend_str .= $name;
        }

        $result['category'] = array_keys($datalist);
        return $result;
    }

    /**
     * 柱状图和曲线图统计-无分组
     * @author Created by Xing <464401240@qq.com>
     */
    private static function totalChartGraphNotGroup($row, $starttime, $endtime)
    {
        $totalseconds = $endtime - $starttime;
        if ($totalseconds > 86400 * 30 * 2) {
            $format = '%Y-%m';
        } else {
            if ($totalseconds > 86400) {
                $format = '%Y-%m-%d';
            } else {
                $format = '%H:00';
            }
        }

        if ($row->type_total == 'sum') {
            if ($row->field_time_type == 'int' || $row->field_time_type == 'bigint') {
                $sql = 'SUM(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(FROM_UNIXTIME(' . $row->name . '.' . $row->field_time . '), "' . $format . '") AS field_time';
            } else {
                $sql = 'SUM(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(' . $row->name . '.' . $row->field_time . ', "' . $format . '") AS field_time';
            }
        } else {
            if ($row->field_time_type == 'int' || $row->field_time_type == 'bigint') {
                $sql = 'COUNT(' . $row->is_distinct . ' ' . $row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(FROM_UNIXTIME(' . $row->name . '.' . $row->field_time . '), "' . $format . '") AS field_time';
            } else {
                $sql = 'COUNT(' . $row->is_distinct . ' ' .$row->name . '.' . $row->field_total . ') AS amount,DATE_FORMAT(' . $row->name . '.' . $row->field_time . ', "' . $format . '") AS field_time';
            }
        }

        $data = Db::table($row->name)
            ->where($row->where)
            ->where($row->field_time, 'between time', [$starttime, $endtime])
            ->field("{$sql}")
            ->group('field_time')
            ->select();

        if ($totalseconds > 84600 * 30 * 2) {
            $starttime = strtotime('last month', $starttime);
            while (($starttime = strtotime('next month', $starttime)) <= $endtime) {
                $column[] = date('Y-m', $starttime);
            }
        } else {
            if ($totalseconds > 86400) {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("Y-m-d", $time);
                    $time += 86400;
                }
            } else {
                for ($time = $starttime; $time <= $endtime;) {
                    $column[] = date("H:00", $time);
                    $time += 3600;
                }
            }
        }
        $datalist = array_fill_keys($column, 0);
        foreach ($data as $k => $v) {
            $datalist[$v['field_time']] = isset($datalist[$v['field_time']]) ? round($v['amount'], 2) : 0;
        }

        //组装数据
        $result = [
            'category' => [],
            'data' => [],
            'id' => $row->id,
            'legend_title' => [],
            'unit' => $row->unit,
            'chart_type' => $row->chart_type,
            'title' => $row->title,
            'subtext' => $row->subtext
        ];
        $result['legend_title'][] = $row->legend_title;
        $result['series'][] = [
            'name' => $row->legend_title,
            'type' => $row->chart_type == 'graph' ? 'line' : 'bar',
            'unit' => $row->unit,
            'data' => array_values($datalist),
            'markPoint' => [
                'data' => [
                    ['type' => 'max', 'name' => '最大值'],
                    ['type' => 'min', 'name' => '最小值']
                ]
            ],
            'markLine' => [
                'data' => [
                    ['type' => 'average', 'name' => '平均值']
                ]
            ]
        ];

        $result['category'] = array_keys($datalist);
        return $result;
    }

    /**
     * @notes 排行统计-和
     * @return array
     * @author 兴
     * @date 2022/9/19 14:17
     */
    private static function totalRankingSum($row)
    {
        if ($row->join_table) {
            //有关联
            $total = Db::table($row->name)
                ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                ->where($row->where)
                ->whereTime("{$row->name}.{$row->field_time}", $row->type_time)
                ->sum("{$row->name}.{$row->field_total}");

            $data = Db::table($row->name)
                ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                ->where($row->where)
                ->whereTime("{$row->name}.{$row->field_time}", $row->type_time)
                ->field("{$row->join_table}.{$row->field_show} as field_show,SUM({$row->name}.{$row->field_total}) as nums,{$row->name}.{$row->group_field}")
                ->group("{$row->name}.{$row->group_field}")
                ->order("nums", "desc")
                ->limit($row->show_num)
                ->select();
        } else {
            $total = Db::table($row->name)
                ->where($row->where)
                ->whereTime($row->field_time, $row->type_time)
                ->sum($row->field_total);

            $data = Db::table($row->name)
                ->where($row->where)
                ->whereTime($row->field_time, $row->type_time)
                ->field("SUM({$row->field_total}) as nums,{$row->group_field}")
                ->group($row->group_field)
                ->order("nums", "desc")
                ->limit($row->show_num)
                ->select();
        }

        foreach ($data as $index => $item) {
            if ($row->join_table) {
                $name = $item['field_show'];
            } else {
                $dictionary = json_decode($row->dictionary, true);
                $name = $dictionary[$item[$row->group_field]] ?? $row->group_field . '(' . $item[$row->group_field] . ')';
            }
            $data[$index]['name'] = $name;
            $data[$index]['ratio'] = $total > 0 ? round(($item['nums'] / $total) * 100, 2) : 0;
        }

        $typeTime = ['today' => '今日', 'week' => '本周', 'month' => '本月'];
        $row->title .= isset($typeTime[$row->type_time]) ? "({$typeTime[$row->type_time]})" : '';

        return [
            'total' => $total,
            'data' => $data,
            'title' => $row->title,
            'unit' => $row->unit,
        ];
    }

    /**
     * @notes 排行统计-行
     * @return array
     * @author 兴
     * @date 2022/9/19 14:17
     */
    private static function totalRankingCount($row)
    {
        if ($row->join_table) {
            //有关联
            $total = Db::table($row->name)
                ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                ->where($row->where)
                ->whereTime("{$row->name}.{$row->field_time}", $row->type_time)
                ->count("{$row->name}.{$row->field_total}");

            $data = Db::table($row->name)
                ->join($row->join_table, $row->join_table . '.' . $row->local_key . ' = ' . $row->name . '.' . $row->foreign_key)
                ->where($row->where)
                ->whereTime("{$row->name}.{$row->field_time}", $row->type_time)
                ->field("{$row->join_table}.{$row->field_show} as field_show,COUNT({$row->name}.{$row->field_total}) as nums,{$row->name}.{$row->group_field}")
                ->group("{$row->name}.{$row->group_field}")
                ->order("nums", "desc")
                ->limit($row->show_num)
                ->select();
        } else {
            $total = Db::table($row->name)
                ->where($row->where)
                ->whereTime($row->field_time, $row->type_time)
                ->count($row->field_total);

            $data = Db::table($row->name)
                ->where($row->where)
                ->whereTime($row->field_time, $row->type_time)
                ->field("COUNT({$row->field_total}) as nums,{$row->group_field}")
                ->group($row->group_field)
                ->order("nums", "desc")
                ->limit($row->show_num)
                ->select();
        }

        foreach ($data as $index => $item) {
            if ($row->join_table) {
                $name = $item['field_show'];
            } else {
                $dictionary = json_decode($row->dictionary, true);
                $name = $dictionary[$item[$row->group_field]] ?? $row->group_field . '(' . $item[$row->group_field] . ')';
            }
            $data[$index]['name'] = $name;
            $data[$index]['ratio'] = $total > 0 ? round(($item['nums'] / $total) * 100, 2) : 0;
        }

        $typeTime = ['today' => '今日', 'week' => '本周', 'month' => '本月'];
        $row->title .= isset($typeTime[$row->type_time]) ? "({$typeTime[$row->type_time]})" : '';

        return [
            'total' => $total,
            'data' => $data,
            'title' => $row->title,
            'unit' => $row->unit,
        ];
    }

    /**
     * @notes 图表数据合并(类型、标题、描述一样即可合并到一个统计图上)
     * @author 兴
     * @date 2023/5/22 20:50
     */
    private static function dataMerge($data)
    {
        foreach ($data as $key => &$val) {
            foreach ($data as $k => $v) {
                if ($key != $k && $val['chart_type'] == $v['chart_type'] && $val['title'] == $v['title'] && $val['subtext'] == $v['subtext']) {
                    if ($val['chart_type'] == 'pie') {
                        $val['data'] = array_merge($val['data'], $v['data']);
                        $val['category'] = array_merge($val['category'], $v['category']);
                    } else {
                        $val['legend_title'] = array_merge($val['legend_title'], $v['legend_title']);
                        if (isset($val['series']) && isset($v['series'])){
                            $val['series'] = array_merge($val['series'], $v['series']);
                        }
                    }
                    unset($data[$k]);
                }
            }
        }
        unset($val);
        return array_values($data);
    }
}
