<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel smart\rbac\models\AuthActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Auth Actions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-action-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
<!--        --><?/*= Html::a(Yii::t('app', 'Create Auth Action'), ['create'], ['class' => 'btn btn-success']) */?>
        <?= Html::a(Yii::t('app', 'Refresh'), ['refresh'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'description:ntext',
            'name',
            //'type',
            //'rule_name',
            //'data',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'label' => '是否是菜单',
                'value' => function ($model) {
                    if ($model->menu) {
                        return '是';
                    }
                }
            ],
            [
                'label' => '',
                'value' => function ($model) {
                    if (!$model->menu) {
                        return Html::a('设置为菜单', ['menu/create', 'action_id' => $model->name], ['class' => 'btn-link']);

                    }
                },
                'format' => 'raw'
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
