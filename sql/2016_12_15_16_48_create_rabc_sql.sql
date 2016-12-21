DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'user_id',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'name',
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'email',
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'password',
  `head_portrait` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'head portrait',
  `type` enum('SUPER_ADMIN','ADMIN','USER') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '身份',
  `can_login` tinyint(1) NOT NULL COMMENT '是否可以登录',
  `is_deleted` tinyint(1) NOT NULL COMMENT '是否删除',
  `is_ldap_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否ldap登录用户',
  `create_time` int(11) NOT NULL,
  `modified_time` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表';

DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '权限名称',
  `controller` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'controller name',
  `method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'function name',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('SERVICE','MENU','PAGE') COLLATE utf8_unicode_ci DEFAULT 'MENU',
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限表';

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `menu_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '菜单名称',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_close` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='菜单表';


DROP TABLE IF EXISTS `menu_permission_rdd`;
CREATE TABLE `menu_permission_rdd` (
  `menu_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  KEY `idx_menu_permission_rdd_menu_id` (`menu_id`),
  KEY `idx_menu_permission_rdd_permission_id` (`permission_id`),
  CONSTRAINT `fk_menu_permission_rdd_menu_id` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`),
  CONSTRAINT `fk_menu_permission_rdd_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='菜单和权限关联表';

DROP TABLE IF EXISTS `menu_rdd`;
CREATE TABLE `menu_rdd` (
  `parent_menu_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `icon` varchar(255) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  KEY `idx_menu_rdd_parent_menu_id` (`parent_menu_id`),
  KEY `idx_menu_rdd_menu_id` (`menu_id`),
  CONSTRAINT `fk_menu_rdd_menu_id` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`),
  CONSTRAINT `fk_menu_rdd_parent_menu_id` FOREIGN KEY (`parent_menu_id`) REFERENCES `menu` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='菜单无限级关联表';

DROP TABLE IF EXISTS `permission_group`;
CREATE TABLE `permission_group` (
  `permission_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_number` int(11) DEFAULT NULL COMMENT '编号',
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '权限组名称',
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '别名',
  `is_deleted` tinyint(1) NOT NULL,
  `is_editor` int(1) NOT NULL DEFAULT '1' COMMENT '是否编辑',
  `is_display` int(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`permission_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限组';

DROP TABLE IF EXISTS `permission_group_user_permission_rdd`;
CREATE TABLE `permission_group_user_permission_rdd` (
  `user_id` int(11) DEFAULT NULL,
  `permission_group_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  KEY `idx_permission_group_user_permission_rdd_user_id` (`user_id`),
  KEY `idx_permission_group_user_permission_rdd_permission_group_id` (`permission_group_id`),
  KEY `idx_permission_group_user_permission_rdd_permission_id` (`permission_id`),
  CONSTRAINT `fk_permission_group_user_permission_rdd_permission_group_id` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`permission_group_id`),
  CONSTRAINT `fk_permission_group_user_permission_rdd_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`),
  CONSTRAINT `fk_permission_group_user_permission_rdd_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限组用户和权限组权限关联表';

DROP TABLE IF EXISTS `permission_permission_group_rdd`;
CREATE TABLE `permission_permission_group_rdd` (
  `permission_id` int(11) DEFAULT NULL,
  `permission_group_id` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  KEY `idx_permission_permission_group_rdd_permission_id` (`permission_id`),
  KEY `idx_permission_permission_group_rdd_permission_group_id` (`permission_group_id`),
  CONSTRAINT `fk_permission_permission_group_rdd_permission_group_id` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`permission_group_id`),
  CONSTRAINT `fk_permission_permission_group_rdd_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='权限和权限组关联表';

DROP TABLE IF EXISTS `user_permission_group_rdd`;
CREATE TABLE `user_permission_group_rdd` (
  `user_id` int(11) DEFAULT NULL,
  `permission_group_id` int(11) DEFAULT NULL,
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) NOT NULL,
  KEY `idx_user_permission_group_rdd_user_id` (`user_id`),
  KEY `idx_user_permission_group_rdd_permission_group_id` (`permission_group_id`),
  CONSTRAINT `fk_user_permission_group_rdd_permission_group_id` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`permission_group_id`),
  CONSTRAINT `fk_user_permission_group_rdd_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户和权限组关联表';
