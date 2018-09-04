<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model smart\rbac\models\Menu */

$this->title = 'Update Menu: ' . $model->menu_id;
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->menu_id, 'url' => ['view', 'id' => $model->menu_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="menu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'parentMenu' => $parentMenu

    ]) ?>

</div>
