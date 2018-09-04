<?php

namespace smart\rbac\controllers;

use smart\rbac\exception\UserException;
use smart\rbac\models\AuthAction;
use smart\rbac\models\AuthItemChild;
use Yii;
use smart\rbac\models\AuthRole;
use smart\rbac\models\AuthRoleSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RoleController implements the CRUD actions for AuthRole model.
 * @title 角色管理
 */
class RoleController extends Controller
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
     * Lists all AuthRole models.
     * @title 角色列表a
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthRoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthRole model.
     * @param string $id
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
     * Creates a new AuthRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //print_r($_POST);exit;
        try {
            $model = new AuthRole();

            if (Yii::$app->request->isGet) {
                throw new UserException();
            }
            $model->created_at = time();
            $model->updated_at = time();
            $model->type = AuthRole::TYPE_ROLE;

            $post = Yii::$app->request->post();

            if (!$model->load($post) or !$model->save()) {
                throw new UserException('角色信息保存失败');
            }

            $this->saveRoleActions($model->name, $post['AuthItemChild']);

            Yii::$app->session->setFlash('success', '保存成功');
            return $this->refresh();

        } catch (\Exception $e) {
            return $this->render('create', [
                'model' => $model,
                'actions' => $this->getActions()

            ]);
        }
    }


    /**
     * 保存角色的action明细
     * @param $roleName
     * @param $childs
     * @return bool
     */
    private function saveRoleActions($roleName, $childs)
    {
        $newData = [];
        if (!empty($childs)) {
            foreach ($childs as $child) {
                $AuthItemChild = AuthItemChild::findOne(['parent' => $roleName, 'child' => $child]);
                if (!$AuthItemChild) {
                    $AuthItemChild = new AuthItemChild;
                    $AuthItemChild->parent = $roleName;
                    $AuthItemChild->child = $child;
                }
                $AuthItemChild->save();

                $newData[$AuthItemChild->parent . $AuthItemChild->child] = $AuthItemChild;
            }
        }


        $dbData = [];
        $allData = AuthItemChild::findAll(['parent'=>$roleName]);
        if($allData)
        foreach($allData as $model){
            $dbData[$model->parent.$model->child] = $model;
        }
        $delList = array_diff_key($dbData, $newData);//差集  被删除的action
        foreach ($delList as $model) {
            $model->delete();
        }

        return true;


    }

    /**
     * Updates an existing AuthRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        try {

            if (Yii::$app->request->isGet) {
                throw new UserException();
            }

            $post = Yii::$app->request->post();

            if (!$model->load($post) or !$model->save()) {
                throw new UserException('角色信息保存失败');
            }

            $this->saveRoleActions($model->name, $post['AuthItemChild']);

            Yii::$app->session->setFlash('success', '保存成功');
            return $this->refresh();

        } catch (\Exception $e) {
            return $this->render('update', [
                'model' => $model,
                'actions' => $this->getActions()

            ]);
        }

    }

    private function getActions()
    {
        $actions = AuthAction::find()
            ->where(['type' => AuthAction::TYPE_ACTION])
            ->asArray()
            ->all();
        if ($actions)
            foreach ($actions as &$row) {
                $data = AuthAction::parseDescription($row['description']);

                $row = array_merge($row,$data);
            }
        $actions = ArrayHelper::index($actions, null, 'module_title');

        if ($actions)
            foreach ($actions as $module_name => &$ctrls) {
                $ctrls = ArrayHelper::index($ctrls, null, 'ctrl_title');
            }
        //print_r($actions);exit;


        return $actions;
    }


    /**
     * Deletes an existing AuthRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthRole::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
