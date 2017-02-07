<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Your profile</h1>

<?php
$form = ActiveForm::begin([
            'id' => 'edit-form',
            'options' => ['class' => 'form-horizontal'],
        ]);
?>

<?= $form->field($model, 'username')->textInput()->label('Username') ?>
<?= $form->field($model, 'firstname')->textInput()->label('Firstname') ?>
<?= $form->field($model, 'lastname')->textInput()->label('Lastname') ?>
<?= $form->field($model, 'newpassword')->passwordInput()->label('Password') ?>
<?= $form->field($model, 'newpassword_repetition')->passwordInput()->label('Repeat password') ?>


<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancel', ['/site/index'], ['class' => 'btn btn-warning']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>