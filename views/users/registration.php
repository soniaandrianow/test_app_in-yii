<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin([
            'id' => 'signup-form',
            'options' => ['class' => 'form-horizontal'],
        ]);
?>

<?= $form->field($model, 'username')->textInput()->label('Username') ?>
<?= $form->field($model, 'password')->passwordInput()->label('Password') ?>
<?= $form->field($model, 'password_repetition')->passwordInput()->label('Repeat password') ?>
<?= $form->field($model, 'firstname')->textInput()->label('Firstname') ?>
<?= $form->field($model, 'lastname')->textInput()->label('Lastname') ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>