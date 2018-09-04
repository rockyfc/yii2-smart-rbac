<?php

namespace smart\rbac\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class User extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', '账号'),
            'auth_key' => Yii::t('app', '授权Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', '状态'),
            'created_at' => Yii::t('app', '添加时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }

    private $_roleIds = null;
    public function getRoleIds()
    {
        if($this->_roleIds!==null){
            return $this->_roleIds;
        }

        $tmp = [];
        if( $this->role )
        {
            foreach($this->role as $role){
                $tmp[] = $role->name;
            }
        }

        $this->_roleIds = $tmp;
        return $this->_roleIds;
    }

    /**
     * 获取用户状态名称数组
     * @return array
     */
    public static function getStatusArray()
    {
        return [
            static::STATUS_DELETED => '不可用',
            static::STATUS_ACTIVE => '可用',
        ];
    }

    public function getRole()
    {
        return $this->hasMany(AuthRole::className(), ['name' => 'item_name'])
            ->viaTable(AuthAssignment::tableName(),['user_id'=>'id']);
    }






}
