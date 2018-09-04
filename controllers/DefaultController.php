<?php

namespace smart\rbac\controllers;

use yii\web\Controller;

/**
 * Default controller for the `rbac` module
 * @title 未使用
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
