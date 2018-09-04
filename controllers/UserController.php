<?php

namespace smart\rbac\controllers;

use smart\rbac\exception\UserException;
use smart\rbac\models\SignupForm;
use Yii;
use smart\rbac\models\User;
use smart\rbac\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //$model = new User();
        $model = new SignupForm();

        try {
            if (Yii::$app->request->isGet) {
                throw new UserException();
            }

            if (!$model->load(Yii::$app->request->post()) or !$model->save()) {
                throw new UserException('保存失败');
            }


            Yii::$app->session->setFlash('success', '保存成功');
            return $this->refresh();

        } catch (\Exception $e) {
            if (Yii::$app->request->isPost) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }


    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $model = new SignupForm();

        $model->load(['user'=>$user->toArray()],'user');
        $model->id = $user->id;
        $model->role_ids = $user->getRoleIds();

        //print_r($user->getRoleIds());exit;
        try {
            if (Yii::$app->request->isGet) {
                throw new UserException();
            }


            if (!$model->load(Yii::$app->request->post()) or !$model->save()) {
                throw new UserException('保存失败');

            }

            Yii::$app->session->setFlash('success', '保存成功');
            return $this->refresh();

        } catch (\Exception $e) {
            if (Yii::$app->request->isPost) {
                Yii::$app->session->setFlash('error', $e->getMessage().$e->getTraceAsString());
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }








    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
