
-- ----------------------------
-- Records of __PREFIX__customcharts_total_number
-- ----------------------------
INSERT INTO `__PREFIX__customcharts_total_number` (`id`, `name`, `title`, `field_total`, `type_total`, `field_time`, `field_time_type`, `type_time`, `where`, `icon`, `icon_color`, `weigh`, `is_money`) VALUES
(1, '__PREFIX__user', '会员总数', 'id', 'count', 'jointime', 'bigint', 'all', '', 'fa fa-user-o', '#00ccff', 1, 0),
(2, '__PREFIX__admin_log', '管理员操作数', 'id', 'count', 'createtime', 'bigint', 'all', '', 'fa fa-dashboard', '#cc6b6b', 2, 0),
(3, '__PREFIX__attachment', '附件总数', 'id', 'count', 'createtime', 'bigint', 'all', '', 'fa fa-file-photo-o', '#7bd387', 3, 0),
(4, '__PREFIX__admin', '管理员数', 'id', 'count', 'createtime', 'bigint', 'all', '', 'fa fa-user', '#446af5', 4, 0);

-- ----------------------------
-- Records of __PREFIX__customcharts_ranking
-- ----------------------------
INSERT INTO `__PREFIX__customcharts_ranking` (`id`, `name`, `field_total`, `type_total`, `group_field`, `field_time`, `field_time_type`, `type_time`, `unit`, `title`, `where`, `weigh`, `show_num`, `join_table`, `foreign_key`, `local_key`, `field_show`) VALUES
(1, '__PREFIX__admin_log', 'id', 'count', 'admin_id', 'createtime', 'bigint', 'today', '操作次数', '管理员操作排行', '', 1, 5, '__PREFIX__admin', 'admin_id', 'id', 'nickname'),
(2, '__PREFIX__admin_log', 'id', 'count', 'admin_id', 'createtime', 'bigint', 'week', '操作次数', '管理员操作排行', '', 1, 5, '__PREFIX__admin', 'admin_id', 'id', 'nickname'),
(3, '__PREFIX__admin_log', 'id', 'count', 'admin_id', 'createtime', 'bigint', 'month', '操作次数', '管理员操作排行', '', 1, 5, '__PREFIX__admin', 'admin_id', 'id', 'nickname');

-- ----------------------------
-- Records of __PREFIX__customcharts_chart
-- ----------------------------
INSERT INTO `__PREFIX__customcharts_chart` (`id`, `name`, `field_total`, `type_total`, `group_field`, `field_time`, `field_time_type`, `chart_type`, `legend_title`, `unit`, `title`, `subtext`, `where`, `weigh`, `join_table`, `foreign_key`, `local_key`, `field_show`, `dictionary`) VALUES
(1, '__PREFIX__admin_log', 'id', 'count', 'admin_id', 'createtime', 'bigint', 'graph', '操作数', '次', '管理员操作数', '系统管理员操作次数分析', '', 1, 'fa_admin', 'admin_id', 'id', 'nickname', ''),
(2, '__PREFIX__user', 'id', 'count', 'gender', 'logintime', 'bigint', 'pie', '性别', '人', '男女比例分析', '分析所有会员性别占比', '', 2, '', 'id', 'id', 'nickname', '{\"0\":\"女\",\"1\":\"男\"}'),
(3, '__PREFIX__admin', 'id', 'count', '', 'createtime', 'bigint', 'histogram', '创建人数', '人', '管理员统计-柱状图', '演示合并统计', '', 3, '', 'id', '', '', ''),
(4, '__PREFIX__admin', 'id', 'count', '', 'logintime', 'bigint', 'histogram', '登录人数', '人', '管理员统计-柱状图', '演示合并统计', '', 4, '', 'id', '', '', ''),
(5, '__PREFIX__admin', 'id', 'count', '', 'createtime', 'bigint', 'graph', '创建人数', '人', '管理员统计-曲线图', '演示合并统计', '', 5, '', 'id', '', '', ''),
(6, '__PREFIX__admin', 'id', 'count', '', 'logintime', 'bigint', 'graph', '登录人数', '人', '管理员统计-曲线图', '演示合并统计', '', 6, '', 'id', '', '', '');