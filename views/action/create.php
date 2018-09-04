<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model smart\rbac\models\AuthAction */

$this->title = Yii::t('app', 'Create Auth Action');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Auth Actions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-action-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
