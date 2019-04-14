<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
use yii\widgets\Pjax;

$this -> title = 'Зарегистрироваться';
$this -> params['breadcrumbs'][] = $this -> title;
?>


<div class="createUser">
    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'enableAjaxValidation' => true,
    ]) ?>

    <?= $form -> field($model, 'username') -> textInput() ?>
    <?= $form -> field($model, 'email') -> textInput() ?>
    <?= $form -> field($model, 'password_repeat') -> passwordInput() ?>
    <?= $form -> field($model, 'password') -> passwordInput() ?>

    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>
</div>