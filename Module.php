<?php

namespace smart\rbac;

/**
 * rbac module definition class
 * @title 权限管理
 */
class Module extends \yii\base\Module
{
    /**
     * 不需要纳入权限管理的Module
     * @var array
     */
    public $skipOn = ['gii', 'debug'];

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'smart\rbac\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
