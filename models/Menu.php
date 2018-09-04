<?php

namespace smart\rbac\models;

use smart\rbac\libs\TreeHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "smart_menu".
 *
 * @property int $menu_id 菜单id
 * @property int $parent_id 上级菜单
 * @property string $menu_name 菜单名称
 * @property string $url 链接地址
 * @property string $icon 菜单icon图
 * @property int $create_at 创建时间
 * @property int $update_at 更新时间
 * @property int $action_id 当前菜单关联的actionId
 * @property int $order_by 排序值，越大越靠前
 * @property int $status 是否可用1：不可用 2：可用
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * 状态：可用
     */
    const STATUS_ENABLED = 2;

    /**
     * 状态：不可用
     */
    const STATUS_DISABLED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'smart_menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'create_at', 'update_at',  'order_by', 'status'], 'integer'],
            [['menu_name', 'update_at'], 'required'],
            [['menu_name'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 300],
            [['icon','action_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => Yii::t('app', '菜单id'),
            'parent_id' => Yii::t('app', '上级菜单'),
            'menu_name' => Yii::t('app', '名称'),
            'url' => Yii::t('app', '链接地址'),
            'icon' => Yii::t('app', '图标'),
            'create_at' => Yii::t('app', '创建时间'),
            'update_at' => Yii::t('app', '更新时间'),
            'action_id' => Yii::t('app', 'actionId'),//当前菜单关联的
            'order_by' => Yii::t('app', '排序值'),//越大越靠前
            'status' => Yii::t('app', '是否可用'), //1：不可用 2：可用
        ];
    }

    /**
     * 获取是否可用数组
     * @return array
     */
    public static function getStatusArray()
    {
        return [
            static::STATUS_DISABLED => '已禁用',
            static::STATUS_ENABLED => '可用',
        ];
    }

    /**
     * 获取tree形下拉列表
     * @param array $condition
     * @return mixed
     */
    public static function treeArray(array $condition = [])
    {
        $list = static::find()
            ->select('menu_id,menu_name,parent_id')
            ->where($condition)
            ->asArray()
            ->all();

        $TreeHelper = new TreeHelper($list, 'menu_name', 'menu_id', 'parent_id');

        $list = $TreeHelper->format(0);

        $tmp = [];
        if ($list)
            foreach ($list as $k => $v) {
                $tmp[$v['menu_id']] = $v['menu_name'];
            }

        return ArrayHelper::merge([
            '0' => '请选择'
        ], $tmp);
    }

}
