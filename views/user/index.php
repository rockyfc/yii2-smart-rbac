<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel smart\rbac\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\Column',
                'header' => 'ID',
                'content' => function ($model) {
                    return $model->id;
                }
            ],
            //'id',
            'username',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            'email:email',
            'status',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return \smart\rbac\models\User::getStatusArray()[$model->status];
                }
            ],
            [
                'label' => '角色',
                'value' => function ($model) {
                    if ($model->role) {
                        $arr = \yii\helpers\ArrayHelper::getColumn($model->role, 'name');
                        return implode(', ', $arr);
                    }

                }
            ],
            //'created_at',
            //'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
