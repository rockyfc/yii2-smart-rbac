<?php

namespace smart\rbac\models;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string $description
 * @property string $rule_name
 * @property resource $data
 * @property int $created_at
 * @property int $updated_at
 *
 */
class AuthAction extends \yii\db\ActiveRecord
{
    const TYPE_ACTION = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        //return 'auth_item';
        return Yii::$app->authManager->itemTable;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', '路由'),
            'type' => Yii::t('app', '类型'),
            'description' => Yii::t('app', '描述'),
            'rule_name' => Yii::t('app', 'Rule Name'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }

    /**
     * 将controller 名称转化成路由形式
     * @param $controllerName
     * @return string
     */
    public static function convertToControllerId($controllerName)
    {

        $str = preg_replace("/(?=[A-Z])/", '-', $controllerName);
        $str = trim($str, '-');
        $str = strtolower($str);
        $arr = explode('-', $str);
        $last = count($arr) - 1;
        if ($arr[$last] === 'controller') {
            unset($arr[$last]);
        }

        return implode('-', $arr);
    }

    /**
     * 将action的名称转化成路由形式
     * @param $actionId
     * @return string
     */
    public static function convertToActionId($actionId)
    {
        /*
        preg_match_all("/([a-zA-Z]{1}[a-z]*)?[^A-Z]/",$str,$array);
        */
        $str = preg_replace("/(?=[A-Z])/", '-', $actionId);
        $str = strtolower($str);
        $arr = explode('-', $str);
        if ($arr[0] === 'action') {
            array_shift($arr);
        }

        return implode('-', $arr);
    }

    /**
     * 生成路由格式
     * @param null $moduleId
     * @param null $controllerId
     * @param null $actionId
     * @return string
     */
    public static function generateRoute($moduleId = null, $controllerId = null, $actionId = null)
    {
        $params = [
            $moduleId,
            $controllerId,
            $actionId
        ];

        return '/'.implode('/', $params);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['action_id' => 'name']);
    }


    /**
     * 将描述信息分解成三个字段，方便使用
     * @param $description
     * @return array
     */
    public static function parseDescription($description)
    {
        $tmp = explode('/', $description);
        $row = [];
        $row['module_title'] = trim($tmp['0']);
        $row['ctrl_title'] = trim($tmp['1']);
        $row['action_title'] = trim($tmp['2']);

        return $row;
    }
}
