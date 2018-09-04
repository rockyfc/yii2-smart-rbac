<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model smart\rbac\models\AuthRole */

$this->title = Yii::t('app', 'Create Auth Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Auth Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-role-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'actions' => $actions,
    ]) ?>

</div>
