<?php
namespace smart\rbac\components;


class DbManager extends \yii\rbac\DbManager
{
    /**
     * 可授权的用户数据表名称
     * @var string
     */
    public $userTable = '{{user}}';

    /**
     * 菜单数据表名称
     * @var string
     */
    public $menuTable = '{{menu}}';

}