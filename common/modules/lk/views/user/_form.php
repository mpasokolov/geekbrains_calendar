<?php

use yii\bootstrap\ActiveForm;

?>

<?php $form = ActiveForm::begin() ?>

<?= $form -> field($user, 'username') -> textInput() ?>
<?= $form -> field($user, 'email') -> textInput() ?>
<?= $form -> field($user, 'password_repeat') -> textInput() ?>
<?= $form -> field($user, 'password') -> textInput(['value' => '']) ?>

<?= \yii\bootstrap\Html::submitButton('Изменить данные', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>
