<?php
/**
 * Created by PhpStorm.
 * User: Brian
 * Date: 2015/11/2
 * Time: 16:12
 */

return [
    [
        'prefix' => 'Admin',
        'domain' => 'ldev.admin-permission.putao.com',
        'routes' => [
            ['GET', '/', 'core/Index/index'],
            ['GET', 'signin', 'core/Index/signinPage'],                                                             // 登录页面
            ['GET', 'service/signin', 'core/Index/signin'],                                                         // 登录
            ['GET', 'logout', 'core/Index/logout'],
            ['GET', 'user/userlist', 'core/user/userlist'],
            ['GET', 'menu/index', 'core/menu/index'],
            ['GET', 'permgroup/index', 'core/permgroup/index'],
            ['GET', 'system/clearcache', 'core/system/clearcache'],
            ['POST', 'doclearpost', 'core/System/doClearCache'],                                                    // 清理缓存
            ['GET', 'permission/index', 'core/permission/index'],
            ['GET', 'user', 'core/User/userList'],                                                                  // 用户列表
            ['GET', 'user/add', 'core/User/userAdd'],                                                               // 管理员添加页面
            ['POST', 'service/adduserpost', 'core/User/userAddPost'],                                               // 管理员添加
            ['POST', 'service/userval', 'core/User/userVal'],                                                       // 用户状态修改
            ['POST', 'service/userdel', 'core/User/userDel'],                                                       // 用户删除
            ['POST', 'user/permission/<uid:\d+>', 'core/User/uesrPermission'],                                      // 用户权限设置页面
            ['POST', 'user/permgroupost', 'core/User/permGroupPost'],                                               // 用户组权限设置
            ['POST', 'user/organizepost', 'core/User/organizePost'],                                                // 用户组织结构设置
            ['GET', 'user/getorganize', 'core/User/getOrganize'],                                                   // 获取用户编制
            ['POST', 'user/all', 'core/User/getAllUser'],                                                           // 获取全部用户
            ['GET', 'menu', 'core/Menu/index'],                                                                     // 菜单列表
            ['GET', 'menu/add', 'core/Menu/menuAdd'],                                                               // 菜单添加页面
            ['POST', 'menu/addpost', 'core/Menu/menuAddPost'],                                                      // 菜单添加
            ['POST', 'menu/value', 'core/Menu/menuStatus'],                                                         // 菜单状态修改
            ['POST', 'menu/del', 'core/Menu/menuDel'],                                                              // 菜单删除
            ['GET', 'menu/edit/<mid:\d+>', 'core/Menu/menuEdit'],                                                   // 菜单编辑页面
            ['POST', 'menu/editpost', 'core/Menu/menuEditPost'],                                                    // 菜单编辑
            ['GET', 'permission', 'Core/Permission/index'],                                                         // 权限列表
            ['GET', 'permission/add', 'core/Permission/add'],                                                       // 权限添加页面
            ['POST', 'permission/addpost', 'core/Permission/addPost'],                                              // 权限添加
            ['POST', 'permission/del', 'core/Permission/delete'],                                                   // 权限删除
            ['GET', 'permission/edit/<pid:\d+>', 'core/Permission/edit'],                                           // 权限编辑页面
            ['POST', 'permission/editpost', 'core/Permission/editPost'],                                            // 权限编辑
            ['POST', 'permission/edittype', 'core/Permission/editType'],                                            // 权限类型编辑
            ['GET', 'permgroup', 'core/permgroup/index'],                                                           // 权限组列表
            ['GET', 'permgroup/add', 'core/PermGroup/permGroupAdd'],                                                // 权限组添加页面
            ['POST', 'permgroup/addpost', 'core/PermGroup/permGroupAddPost'],                                       // 权限组添加
            ['POST', 'permgroup/del', 'core/PermGroup/permGroupDel'],                                               // 权限组删除
            ['GET', 'permgroup/edit/<permgroupid:\d+>', 'core/PermGroup/permGroupEdit'],                            // 权限组编辑页面
            ['POST', 'permgroup/editpost', 'core/PermGroup/permGroupEditPost'],                                     // 权限组编辑
            ['POST', 'permgroup/adduser', 'core/PermGroup/addUserPost'],                                            // 添加用户到权限组
            ['POST', 'permgroup/user', 'core/PermGroup/userList'],                                                  // 权限组员工权限配置页面
            ['POST', 'permgroup/usereditpost', 'core/PermGroup/permUserEditPost'],                                  // 权限组员工权限配置
            ['POST', 'permgroup/userperm', 'core/PermGroup/getUserGroupPermission'],                                // 员工在权限组可用权限
            ['POST', 'permgroup/userdel', 'core/PermGroup/userDel'],                                                // 员工在权限组可用权限

            ['GET', 'demo/index', 'core/Demo/index'],                                                               // demo

        ]
    ],
    [
        'prefix' => 'Front',
        'domain' => 'ldev.permission.putao.com',
        'routes' => [
            ['post', '/', 'core/postActiveRecord/index'],                               //index : 200

        ]
    ]
];