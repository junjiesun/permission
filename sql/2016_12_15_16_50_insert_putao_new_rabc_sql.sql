SET SQL_MODE='';
SET FOREIGN_KEY_CHECKS=0;	/*禁用外键约束*/

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '系统设置', '0', '0', unix_timestamp(now()), unix_timestamp(now()), null);
INSERT INTO `menu` VALUES ('2', '用户管理', '0', '0', unix_timestamp(now()), unix_timestamp(now()), null);
INSERT INTO `menu` VALUES ('3', '菜单管理', '0', '0', unix_timestamp(now()), unix_timestamp(now()), null);
INSERT INTO `menu` VALUES ('4', '权限组管理', '0', '0', unix_timestamp(now()), unix_timestamp(now()), null);
INSERT INTO `menu` VALUES ('5', '清理缓存', '0', '0', unix_timestamp(now()), unix_timestamp(now()), '清理缓存');
INSERT INTO `menu` VALUES ('6', '权限管理', '0', '0', unix_timestamp(now()), unix_timestamp(now()), '权限管理');

-- ----------------------------
-- Records of menu_permission_rdd
-- ----------------------------
INSERT INTO `menu_permission_rdd` VALUES ('1', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_permission_rdd` VALUES ('2', '2', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_permission_rdd` VALUES ('3', '3', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_permission_rdd` VALUES ('4', '4', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_permission_rdd` VALUES ('5', '24', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_permission_rdd` VALUES ('6', '26', unix_timestamp(now()), unix_timestamp(now()));

-- ----------------------------
-- Records of menu_rdd
-- ----------------------------
INSERT INTO `menu_rdd` VALUES (null, '1', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_rdd` VALUES ('1', '2', '100', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_rdd` VALUES ('1', '3', '101', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_rdd` VALUES ('1', '4', '102', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_rdd` VALUES ('1', '5', '103', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `menu_rdd` VALUES ('1', '6', '104', unix_timestamp(now()), unix_timestamp(now()));

-- ----------------------------
-- Records of permission
-- ----------------------------
INSERT INTO `permission` VALUES ('1', '系统设置', 'System', '', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('2', '用户管理', 'User', 'userList', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('3', '菜单管理', 'Menu', 'index', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('4', '权限组管理', 'PermGroup', 'index', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('5', '菜单添加页面', 'Menu', 'menuAdd', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('6', '菜单添加', 'Menu', 'menuAddPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('7', '菜单编辑页面', 'Menu', 'menuEdit', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('8', '菜单编辑', 'Menu', 'menuEditPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('9', '菜单状态修改', 'Menu', 'menuStatus', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('10', '菜单删除', 'Menu', 'menuDel', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('11', '管理员添加页面', 'User', 'userAdd', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('12', '管理员添加', 'User', 'userAddPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('13', '用户状态修改', 'User', 'userVal', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('14', '用户删除', 'User', 'userDel', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('15', '用户权限组页面', 'User', 'uesrPermission', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('16', '用户组权限设置', 'User', 'permGroupPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('17', '权限组添加页面', 'PermGroup', 'permGroupAdd', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('18', '权限组添加', 'PermGroup', 'permGroupAddPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('19', '权限组修改页面', 'PermGroup', 'permGroupEdit', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('20', '权限组修改', 'PermGroup', 'permGroupEditPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('21', '删除权限组', 'PermGroup', 'permGroupDel', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('22', '权限组分配用户', 'PermGroup', 'addUserPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('23', '权限组分配用户', 'Organization', 'addUserPost', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('24', '清理缓存页面', 'System', 'clearCache', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('25', '清理缓存', 'System', 'doClearCache', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('26', '权限管理', 'Permission', 'index', '0', 'MENU', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('27', '权限添加页面', 'Permission', 'add', '0', 'PAGE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('28', '权限添加', 'Permission', 'addPost', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('29', '权限删除', 'Permission', 'delete', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('30', '权限编辑页面', 'Permission', 'edit', '0', 'PAGE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('31', '权限编辑', 'Permission', 'editPost', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('32', '权限类型编辑', 'Permission', 'editType', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('33', '单独权限配置页面', 'PermGroup', 'permUserEdit', '0', 'PAGE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('34', '权限组员工列表', 'PermGroup', 'userList', '0', 'PAGE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('35', '员工权限配置', 'PermGroup', 'permUserEditPost', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('36', '员工权限获取', 'PermGroup', 'getUserGroupPermission', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission` VALUES ('37', '权限组员工删除', 'PermGroup', 'userDel', '0', 'SERVICE', unix_timestamp(now()), unix_timestamp(now()));

-- ----------------------------
-- Records of permission_group
-- ----------------------------
INSERT INTO `permission_group` VALUES ('1', null, 'SUPERADMIN', null, '0', '0', '0', unix_timestamp(now()), unix_timestamp(now()), null);
INSERT INTO `permission_group` VALUES ('2', null, 'Public', null, '0', '1', '1', unix_timestamp(now()), unix_timestamp(now()), '公共权限组');

-- ----------------------------
-- Records of permission_permission_group_rdd
-- ----------------------------
INSERT INTO `permission_permission_group_rdd` VALUES ('1', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('2', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('3', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('4', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('5', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('6', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('7', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('8', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('9', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('10', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('11', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('12', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('13', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('14', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('15', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('16', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('17', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('18', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('19', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('20', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('21', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('22', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('23', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('24', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('25', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('26', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('27', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('28', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('29', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('30', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('31', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('32', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('33', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('34', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('35', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('36', '1', unix_timestamp(now()), unix_timestamp(now()));
INSERT INTO `permission_permission_group_rdd` VALUES ('37', '1', unix_timestamp(now()), unix_timestamp(now()));

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', 'admin@putao.com', '4233137d1c510f2e55ba5cb220b864b11033f156', '', 'SUPER_ADMIN', '1', '0', '0', unix_timestamp(now()), unix_timestamp(now()));

-- ----------------------------
-- Records of user_permission_group_rdd
-- ----------------------------
INSERT INTO `user_permission_group_rdd` VALUES ('1', '1', unix_timestamp(now()), unix_timestamp(now()));

SET FOREIGN_KEY_CHECKS=1;	/*启动外键约束*/
