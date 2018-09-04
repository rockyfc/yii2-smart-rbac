<?php

namespace smart\rbac\controllers;

use Yii;
use smart\rbac\models\AuthAction;
use smart\rbac\models\AuthActionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ActionController implements the CRUD actions for AuthAction model.
 * @title Action管理
 */
class ActionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthAction models.
     * @title Action列表
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthActionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @title Action刷新
     * @return \yii\web\Response
     */
    public function actionRefresh()
    {
        $skipOn = (array)$this->module->skipOn;
        $searchModel = new AuthActionSearch();
        $searchModel->refreshAction($skipOn);

        return $this->redirect(['index']);
    }



}
