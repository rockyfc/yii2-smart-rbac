<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel smart\rbac\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menus';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Menu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            /*['class' => 'yii\grid\Column',
                'header' => 'ID',
                'content' => function ($model) {
                    return $model->menu_id;
                }
            ],*/
            [
                'attribute' => 'menu_name',
                'value' => function ($model) {
                    $str = '';
                    if ($model->icon) {
                        $str .= '<span class="glyphicon glyphicon-' . $model->icon . '"></span> ';
                    }
                    $str .= $model->menu_name;

                    return $str;
                },
                'format' => 'raw'
            ],
            'parent_id',
            'url:url',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return \smart\rbac\models\Menu::getStatusArray()[$model->status];
                }
            ],
            'order_by',
            'create_at:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
