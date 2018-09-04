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
 */
class AuthRole extends \yii\db\ActiveRecord
{
    const TYPE_ROLE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
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
            'name' => Yii::t('app', '角色名称'),
            'type' => Yii::t('app', '类型'),
            'description' => Yii::t('app', '描述'),
            'rule_name' => Yii::t('app', 'Rule Name'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hasMany(AuthAction::className(), ['name' => 'child'])
            ->viaTable(AuthItemChild::tableName(), ['parent' => 'name']);
    }


    /**
     * 获取action名称数组
     * @return array
     */
    public function getActionsNames()
    {
        $tmp = [];
        foreach ($this->actions as $action) {
            $tmp[] = $action->name;
        }
        return $tmp;
    }

    /**
     * @param array $condition
     * @return array
     */
    public static function getRoleNameArray($condition = [])
    {
        $condition = array_merge($condition, ['type' => static::TYPE_ROLE]);
        return $arr = static::find()
            ->select('description')
            ->where($condition)
            ->indexBy('name')
            ->column();
    }
}
