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
     * Displays a single AuthAction model.
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
     * Creates a new AuthAction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new AuthAction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Updates an existing AuthAction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing AuthAction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the AuthAction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthAction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthAction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    public function actionRefresh()
    {

        $app = Yii::$app;

        $actionMap = [];

        foreach ($app->getModules() as $moduleId => $model) {
            try {
                if ($Module = $app->getModule($moduleId, true)) {

                    $namespace = $Module->controllerNamespace;
                    if (!preg_match('/^app.*/', $namespace)) {
                        continue;
                    }

                    $path = $Module->getControllerPath();
                    $controllers = glob($path . '/*Controller.php');

                    if ($controllers)
                        foreach ($controllers as $ctrl) {
                            $ctrlName = basename(trim($ctrl, '.php'));
                            $ctrl = $namespace . '\\' . $ctrlName;

                            //ECHO "<BR/>";

                            $ref = new \ReflectionClass($ctrl);
                            //$docCommentArr = explode("\n", $ref->getDocComment());

                            $ctrlTitle = $this->parseComment($ref->getDocComment());
                            if (preg_match('/未使用/', $ctrlTitle) or preg_match('/已弃用/', $ctrlTitle)) {
                                continue;
                            }


                            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

                            $controllerId = AuthAction::convertToControllerId($ctrlName);

                            //
                            if ($methods) {
                                foreach ($methods as $method) {
                                    if (!preg_match("/^action/", $method->name)
                                        or $method->name === 'actionClientValidate'
                                        or $method->name === 'actions'
                                    ) {
                                        //echo $method->name."<br/>";
                                        continue;
                                    }

                                    $actionTitle = $this->parseComment($method->getDocComment());
                                    if (preg_match('/未使用/', $actionTitle) or preg_match('/已弃用/', $actionTitle)) {
                                        continue;
                                    }

                                    $actionId = AuthAction::convertToActionId($method->name);

                                    $route = '/'.$moduleId . '/' . $controllerId . '/' . $actionId;
                                    $model = AuthAction::findOne([
                                        'name' => $route
                                    ]);

                                    if (!$model) {
                                        $model = new AuthAction();
                                        $model->created_at = time();
                                        $model->name = $route;
                                        $model->type = AuthAction::TYPE_ACTION;
                                    }

                                    //$model->action_id;
                                    $moduleTitle = $moduleId;
                                    $actionTitle = empty($actionTitle) ? $actionId : $actionTitle;
                                    $ctrlTitle = empty($ctrlTitle) ? $controllerId : $ctrlTitle;
                                    $model->description = $moduleTitle.' - '.$ctrlTitle.' - '.$actionTitle;

                                    if (!empty($model->getDirtyAttributes()) and $model->save(false)) {
                                        $model->updated_at = time();
                                        $model->save(false);
                                    } else {
                                        //print_r($model->getErrors());
                                    }

                                    //$kk = $model->module_name.'_'.$model->ctrl_name.'_'.$model->action_name;
                                    $actionMap[$model->name] = $model;
                                }
                            }
                            //print_r($actionMap);

                        }

                }

            } catch (\Exception $e) {
                echo $e->getMessage() . "<br/>";
                die();
            }

        }

        $dbActionList = AuthAction::find()->where(['type'=>AuthAction::TYPE_ACTION])->indexBy('name')->all();
        $delList = array_diff_key($dbActionList, $actionMap);//差集  被删除的action
        foreach ($delList as $model) {
            $model->delete();
        }

        return $this->redirect(['index']);
    }


    private function parseComment($str)
    {

        $arr = explode('\n', $str);

        foreach ($arr as $comment) {
            $pos = stripos($comment, '@title');
            if ($pos > 0) {
                $str = substr($comment, $pos + 6);
                $endPos = stripos($str, '*');
                if ($endPos <= 0) {
                    $endPos = stripos($str, ' ');
                }
                $str = substr($str, 0, ($endPos - 1));
                return trim($str);
            }

        }
    }
}
