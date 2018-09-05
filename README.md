Smart Rbac Manager for Yii2
===========================
本应用建立在Yii2的数据结构基础之上，使用之前请先安装yii2框架的rbac推荐的数据表。

由于官方提供的rbac功能界面理解起来有一定的难度，所以在自己项目的开发中重写了一套
RBAC。并把它分享出来。其中包括四个模块：
- Action管理
- 角色管理
- 菜单管理
- 用户授权管理

在开发过程中，为了使用户能方便快速的的进行二次开发，基本代码层次和架构均同yii2框架的Module
书写方法，代码主体代码用gii生成。并且未对view层做过多修饰，必要时可自行调整。


安装方法
------------

推荐使用 [composer](http://getcomposer.org/download/) 安装

或者运行以下命令

```
php composer.phar require --prefer-dist rockyfc/yii2-smart-rbac "*"
```

或者将以下代码写进你的`composer.json` 文件中执行

```
"rockyfc/yii2-smart-rbac": "*"
```

使用方法
-----------

将以下代码添加到你的配置文件中

```php

//权限管理组件
'components' => [
    
    ...其他组件
    
    "authManager" => [
        // yii\rbac\DbManager的增强版
        "class" => 'app\smart\rbac\components\DbManager', 
        "defaultRoles" => ["guest"],
        
        //菜单表名称
        "menuTable" => 'smart_menu',
    
        //可以绑定角色的用户表名称
        "userTable" => 'user', 
    ],
    
    ...其他组件
    
]
```

```php
"modules" => [
    'rbac' => [
        'class' => 'smart\rbac\Module',
        
        //有些Module你并不想添加权限判断，则把它过滤掉
        'skipOn' => ['debug','gii'], 
    ],
]
```


请将菜单表导入数据库（表名称可自定义）
```mysql
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `parent_id` int(11) DEFAULT NULL COMMENT '上级菜单',
  `menu_name` varchar(50) NOT NULL COMMENT '菜单名称',
  `url` varchar(300) NOT NULL DEFAULT '' COMMENT '链接地址',
  `icon` varchar(100) DEFAULT NULL COMMENT '菜单icon图',
  `create_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NOT NULL COMMENT '更新时间',
  `action_id` varchar(100) DEFAULT NULL COMMENT '当前菜单关联的actionId',
  `order_by` int(11) NOT NULL DEFAULT '0' COMMENT '排序值，越大越靠前',
  `status` int(1) NOT NULL DEFAULT '2' COMMENT '是否可用1：不可用 2：可用',
  PRIMARY KEY (`menu_id`) 
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='菜单管理表';
```


我们将 ```@vendor\yiisoft\yii2\rbac\migrations\schema-mysql.sql``` 文件导入数据库，或者通过可视化管理工具、手动等方式创建改文件中的数据表。



访问方法
------------

- 访问ction列表 http://xxxx.com/rbac/rbac/action/index
- 访问Menu列表 http://xxxx.com/rbac/rbac/menu/index
- 访问用户列表 http://xxxx.com/rbac/rbac/user/index
- 访问角色列表 http://xxxx.com/rbac/rbac/role/index









