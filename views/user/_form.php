<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/*$userRoles = \smart\rbac\models\UserRoleRelation::find()
    ->select('role_id')
    ->where(['user_id' => $model->id])
    ->column();*/

/* @var $this yii\web\View */
/* @var $model smart\rbac\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php if($model->id):?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->hint('不填表示不修改') ?>
    <?php else: ?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    <?endif;?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role_ids')->label('角色')->checkboxList(\smart\rbac\models\AuthRole::getRoleNameArray(),['value'=>$model->role_ids])?>

    <?= $form->field($model, 'status')->radioList(\smart\rbac\models\User::getStatusArray()) ?>


    <!--<div class="list-group">
        <dl class="list-group-item">
            <?php
/*            $roles = \smart\rbac\models\Role::find()->all();
            if ($roles) foreach ($roles as $roleModel): */?>
                <dd>
                    <?/*= $form->field($roleModel, 'role_name')->checkbox(['maxlength' => true]) */?>
                </dd>
                <?/* // Html::checkboxList($model->role,\smart\rbac\models\Role::getRoleNameArray(),['value'=>$userRoles])*/?>
            <?php /*endforeach; */?>
        </dl>
    </div>
-->
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
