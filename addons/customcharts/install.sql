-- ----------------------------
-- Table structure for __PREFIX__customcharts_total_number
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__customcharts_total_number` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '表名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `field_total` varchar(50) NOT NULL DEFAULT '' COMMENT '统计字段',
  `type_total` enum('sum','count') NOT NULL DEFAULT 'count' COMMENT '统计类型',
  `field_time` varchar(50) NOT NULL DEFAULT '' COMMENT '时间字段',
  `field_time_type` varchar(20) NOT NULL DEFAULT '' COMMENT '时间字段类型',
  `type_time` enum('today','week','month','all') NOT NULL DEFAULT 'all' COMMENT '时间类型',
  `where` varchar(255) NOT NULL COMMENT '自定义where',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `icon_color` char(7) NOT NULL COMMENT '图标颜色',
  `weigh` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_money` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否金额格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='总数统计表';

-- ----------------------------
-- Table structure for __PREFIX__customcharts_ranking
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__customcharts_ranking` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '表名',
  `field_total` varchar(50) NOT NULL COMMENT '统计字段',
  `type_total` enum('count','sum') NOT NULL DEFAULT 'sum' COMMENT '统计类型',
  `group_field` varchar(50) NOT NULL DEFAULT '' COMMENT '分组字段',
  `field_time` varchar(50) NOT NULL DEFAULT '' COMMENT '时间字段',
  `field_time_type` varchar(20) NOT NULL DEFAULT '' COMMENT '时间字段类型',
  `type_time` enum('today','week','month') NOT NULL DEFAULT 'today' COMMENT '时间类型',
  `unit` varchar(10) NOT NULL COMMENT '单位标题',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `where` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义Where',
  `weigh` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `show_num` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '排行榜显示数量',
  `join_table` varchar(50) NOT NULL DEFAULT '' COMMENT '关联表',
  `foreign_key` varchar(50) NOT NULL DEFAULT '' COMMENT '关联外键',
  `local_key` varchar(50) NOT NULL DEFAULT '' COMMENT '关联主键',
  `field_show` varchar(50) NOT NULL DEFAULT '' COMMENT '显示的字段',
  `dictionary` text COMMENT '分组字典',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='排行统计表';

-- ----------------------------
-- Table structure for __PREFIX__customcharts_chart
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__customcharts_chart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '表名',
  `field_total` varchar(50) NOT NULL DEFAULT '' COMMENT '统计字段',
  `type_total` enum('sum','count') NOT NULL DEFAULT 'count' COMMENT '统计类型',
  `group_field` varchar(50) NOT NULL DEFAULT '' COMMENT '分组字段',
  `field_time` varchar(50) NOT NULL DEFAULT '' COMMENT '时间字段',
  `field_time_type` varchar(20) NOT NULL DEFAULT '' COMMENT '时间字段类型',
  `chart_type` enum('pie','graph','histogram') NOT NULL DEFAULT 'pie' COMMENT '图表类型',
  `legend_title` varchar(50) NOT NULL COMMENT '图例标题',
  `unit` varchar(10) NOT NULL COMMENT '单位',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `subtext` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `where` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义Where',
  `weigh` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `join_table` varchar(50) NOT NULL DEFAULT '' COMMENT '关联表',
  `foreign_key` varchar(50) NOT NULL DEFAULT '' COMMENT '关联外键',
  `local_key` varchar(50) NOT NULL DEFAULT '' COMMENT '关联主键',
  `field_show` varchar(50) NOT NULL DEFAULT '' COMMENT '显示的字段',
  `dictionary` text COMMENT '分组字典',
  `is_distinct` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '是否去重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='图表统计表';


-- ----------------------------
-- 1.0.8
-- ----------------------------
BEGIN;
ALTER TABLE __PREFIX__customcharts_chart ADD field_time_type varchar(20) NOT NULL DEFAULT "";
ALTER TABLE __PREFIX__customcharts_ranking ADD field_time_type varchar(20) NOT NULL DEFAULT "";
COMMIT;

-- ----------------------------
-- 1.0.9
-- ----------------------------
BEGIN;
ALTER TABLE `__PREFIX__customcharts_ranking` ADD `dictionary` TEXT COMMENT '分组字典' AFTER `field_show`;
ALTER TABLE `__PREFIX__customcharts_chart` ADD `dictionary` TEXT COMMENT '分组字典' AFTER `field_show`;
COMMIT;

-- ----------------------------
-- 1.1.9
-- ----------------------------
ALTER TABLE `__PREFIX__customcharts_chart` ADD `is_distinct` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '是否去重' AFTER `dictionary`;
