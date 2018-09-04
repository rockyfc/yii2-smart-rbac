<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model smart\rbac\models\Menu */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')
        ->dropDownList($parentMenu); ?>

    <?= $form->field($model, 'menu_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>

    <?/*= $form->field($model, 'create_at')->textInput() */?><!--

    <?/*= $form->field($model, 'update_at')->textInput() */?>

    --><?/*= $form->field($model, 'action_id')->textInput() */?>


    <?= $form->field($model, 'status')->radioList(\smart\rbac\models\Menu::getStatusArray()) ?>

    <?= $form->field($model, 'order_by')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
