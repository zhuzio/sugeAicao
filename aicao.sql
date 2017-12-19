/*
Navicat MySQL Data Transfer

Source Server         : yuyulu
Source Server Version : 50718
Source Host           : localhost:3306
Source Database       : aicao

Target Server Type    : MYSQL
Target Server Version : 50718
File Encoding         : 65001

Date: 2017-10-16 11:44:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ai_admin
-- ----------------------------
DROP TABLE IF EXISTS `ai_admin`;
CREATE TABLE `ai_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `name` varchar(16) NOT NULL COMMENT '管理员名称',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别 0为男 1为女',
  `phone` varchar(15) DEFAULT NULL COMMENT '电话',
  `email` varchar(32) DEFAULT NULL COMMENT '邮箱',
  `pass` varchar(32) NOT NULL COMMENT '密码',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1为启用 0为禁用',
  `addtime` int(11) DEFAULT NULL COMMENT '注册时间',
  `root` tinyint(4) DEFAULT '1' COMMENT '权限',
  `logintimes` int(11) DEFAULT '0' COMMENT '登陆时间',
  `logintime` int(11) DEFAULT NULL COMMENT '登陆时间',
  `loginip` varchar(15) DEFAULT NULL COMMENT '登录ip',
  `role_id` int(11) NOT NULL COMMENT '所属角色',
  `beizhu` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_admin
-- ----------------------------
INSERT INTO `ai_admin` VALUES ('1', 'admin', '1', '13193835328', '1065673465@qq.com', '000000', '1', '1493608027', '0', '4', '1493608032', '127.0.0.1', '2', null);
INSERT INTO `ai_admin` VALUES ('7', 'test1', '1', '13193835329', '123456@qq.com', '000000', '1', '1507950400', '1', '2', null, null, '4', '000');

-- ----------------------------
-- Table structure for ai_auth
-- ----------------------------
DROP TABLE IF EXISTS `ai_auth`;
CREATE TABLE `ai_auth` (
  `auth_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `auth_name` varchar(20) NOT NULL COMMENT '名称',
  `auth_pid` smallint(6) unsigned NOT NULL COMMENT '父id',
  `auth_c` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `auth_a` varchar(32) NOT NULL DEFAULT '' COMMENT '操作方法',
  `auth_path` varchar(32) DEFAULT NULL COMMENT '全路径',
  `auth_level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '级别',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_auth
-- ----------------------------
INSERT INTO `ai_auth` VALUES ('1', '公告管理', '0', '', '', '0-1', '0');
INSERT INTO `ai_auth` VALUES ('2', '公告列表', '1', 'Article', 'articleList', '1-2', '0');
INSERT INTO `ai_auth` VALUES ('3', '管理员管理', '0', '', '', '0-3', '0');
INSERT INTO `ai_auth` VALUES ('4', '角色管理', '3', 'Admin', 'adminRole', '3-4', '0');
INSERT INTO `ai_auth` VALUES ('5', '权限管理', '3', 'Admin', 'adminPermission', '3-5', '0');
INSERT INTO `ai_auth` VALUES ('6', '管理员列表', '3', 'Admin', 'adminList', '3-6', '0');
INSERT INTO `ai_auth` VALUES ('7', '代理商管理', '0', '', '', '0-7', '0');
INSERT INTO `ai_auth` VALUES ('83', '联创列表', '7', 'Member', 'member1List', '7-83', '0');
INSERT INTO `ai_auth` VALUES ('84', '总代列表', '7', 'Member', 'member2List', '7-84', '0');
INSERT INTO `ai_auth` VALUES ('86', '添加总代', '7', 'Member', 'member2Add', '7-86', '0');
INSERT INTO `ai_auth` VALUES ('87', '分成比例', '0', '', '', '0-87', '0');
INSERT INTO `ai_auth` VALUES ('88', '比例设置', '87', 'Divided', 'dividedSet', '87-88', '0');
INSERT INTO `ai_auth` VALUES ('89', '提货申请', '7', 'Member', 'purchaseApplication', '7-89', '0');
INSERT INTO `ai_auth` VALUES ('90', '添加公告', '1', 'Article', 'articleAdd', '1-90', '0');
INSERT INTO `ai_auth` VALUES ('91', '添加联创', '7', 'Member', 'member1Add', '7-91', '0');

-- ----------------------------
-- Table structure for ai_fhjl
-- ----------------------------
DROP TABLE IF EXISTS `ai_fhjl`;
CREATE TABLE `ai_fhjl` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分红记录',
  `account` varchar(64) NOT NULL COMMENT '账号',
  `desc` varchar(64) DEFAULT NULL COMMENT '分红说明',
  `total` decimal(9,2) DEFAULT NULL COMMENT '账户总额',
  `plus` varchar(9) DEFAULT NULL COMMENT '本次分红',
  `balance` decimal(9,2) DEFAULT NULL COMMENT '加分红后账户余额',
  `create_time` int(11) DEFAULT NULL COMMENT '生成记录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_fhjl
-- ----------------------------

-- ----------------------------
-- Table structure for ai_member
-- ----------------------------
DROP TABLE IF EXISTS `ai_member`;
CREATE TABLE `ai_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代理商ID',
  `account` varchar(64) NOT NULL COMMENT '帐号',
  `pass` varchar(32) NOT NULL COMMENT '密码',
  `name` varchar(32) DEFAULT NULL COMMENT '姓名',
  `m_phone` varchar(11) DEFAULT NULL COMMENT '电话号码',
  `recommend_id` int(11) NOT NULL COMMENT '推荐人id',
  `recommend_account` varchar(64) DEFAULT NULL COMMENT '推荐人帐号',
  `recommend_lc` varchar(64) DEFAULT NULL COMMENT '所属联合创始人帐号',
  `balance` double(10,2) DEFAULT NULL COMMENT '账户余额',
  `reg_ip` varchar(15) DEFAULT NULL COMMENT '注册ip',
  `is_lc` tinyint(1) DEFAULT '0' COMMENT '是否联创 0否 1是',
  `is_zd` tinyint(1) DEFAULT '1' COMMENT '是否总代 0否 1是',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态是否开启 0否 1是',
  `check_time` int(11) DEFAULT '0' COMMENT '审核时间',
  `level` tinyint(1) DEFAULT '0' COMMENT '总代或联创等级 默认为0 1为有一代 2为有二代',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_member
-- ----------------------------
INSERT INTO `ai_member` VALUES ('1', 'wodewode', 'ff92a240d11b05ebd392348c35f781b2', '张三', '13193835329', '0', '0', '0', '1.32', '127.0.0.1', '0', '1', '1', '1508035574', '0');

-- ----------------------------
-- Table structure for ai_news
-- ----------------------------
DROP TABLE IF EXISTS `ai_news`;
CREATE TABLE `ai_news` (
  `id` int(5) NOT NULL COMMENT '公告ID',
  `title` varchar(64) DEFAULT NULL COMMENT '公告标题',
  `content` varchar(255) DEFAULT NULL COMMENT '公告内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_news
-- ----------------------------
INSERT INTO `ai_news` VALUES ('1', 'wode', 'wodewode123');

-- ----------------------------
-- Table structure for ai_notice
-- ----------------------------
DROP TABLE IF EXISTS `ai_notice`;
CREATE TABLE `ai_notice` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `status` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ai_notice
-- ----------------------------
INSERT INTO `ai_notice` VALUES ('1', '免责声明', null, '1');

-- ----------------------------
-- Table structure for ai_role
-- ----------------------------
DROP TABLE IF EXISTS `ai_role`;
CREATE TABLE `ai_role` (
  `role_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL COMMENT '角色名称',
  `role_auth_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '权限ids,1,2,5',
  `role_auth_ac` text COMMENT '模块-操作',
  `desc` varchar(2550) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_role
-- ----------------------------
INSERT INTO `ai_role` VALUES ('2', '超级管理员', '1,2,90,3,4,5,6,7,83,84,86,89,91,87,88,', 'welcome,loginOut,,articleList,articleAdd,,adminRole,adminPermission,adminList,,member1List,member2List,member2Add,purchaseApplication,member1Add,,dividedSet,', '你有公告管理,公告列表,添加公告,管理员管理,角色管理,权限管理,管理员列表,代理商管理,联创列表,总代列表,添加总代,提货申请,添加联创,分成比例,比例设置,的权利');
INSERT INTO `ai_role` VALUES ('4', 'shishi', '7,83,84,', 'welcome,loginOut,,member1List,member2List,', '你有代理商管理,总代列表,二级代理商列表,的权利');

-- ----------------------------
-- Table structure for ai_thsq
-- ----------------------------
DROP TABLE IF EXISTS `ai_thsq`;
CREATE TABLE `ai_thsq` (
  `id` int(11) NOT NULL COMMENT '提货申请 id',
  `t_account` varchar(64) DEFAULT NULL COMMENT '提货人帐号',
  `t_recommend` varchar(64) DEFAULT NULL COMMENT '提货人的推荐人',
  `t_amount` int(7) DEFAULT NULL COMMENT '提货数量',
  `t_time` int(11) DEFAULT '0' COMMENT '提货时间',
  `check_time` int(11) DEFAULT '0' COMMENT '发货确认',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ai_thsq
-- ----------------------------
