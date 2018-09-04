<?php

namespace smart\rbac\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use smart\rbac\models\AuthAction;

/**
 * AuthActionSearch represents the model behind the search form of `smart\rbac\models\AuthAction`.
 */
class AuthActionSearch extends AuthAction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'rule_name', 'data'], 'safe'],
            [['type', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AuthAction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => AuthAction::TYPE_ACTION,//角色用2表示
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'rule_name', $this->rule_name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }


    /**
     * @param array $skipOn
     */
    public function refreshAction(Array $skipOn = [])
    {
        $app = Yii::$app;

        $actionMap = [];

        foreach ($app->getModules() as $moduleId => $model) {

            if(in_array($moduleId,$skipOn)){
                continue;
            }

            try {
                if ($Module = $app->getModule($moduleId, true)) {

                    $namespace = $Module->controllerNamespace;
                    //if (!preg_match('/^app.*/', $namespace)) {
                    //    continue;
                    //}

                    $ModuleRef = new \ReflectionClass($Module);
                    $moduleTitle = $this->parseComment($ModuleRef->getDocComment());
                    if (preg_match('/未使用/', $moduleTitle) or preg_match('/已弃用/', $moduleTitle)) {
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

                                    $route = '/' . $moduleId . '/' . $controllerId . '/' . $actionId;
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
                                    $moduleTitle = empty($moduleTitle)?$moduleId:$moduleTitle;
                                    $actionTitle = empty($actionTitle) ? $actionId : $actionTitle;
                                    $ctrlTitle = empty($ctrlTitle) ? $controllerId : $ctrlTitle;
                                    $model->description = $moduleTitle . ' - ' . $ctrlTitle . ' - ' . $actionTitle;

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

        $dbActionList = AuthAction::find()->where(['type' => AuthAction::TYPE_ACTION])->indexBy('name')->all();
        $delList = array_diff_key($dbActionList, $actionMap);//差集  被删除的action
        foreach ($delList as $model) {
            $model->delete();
        }

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
