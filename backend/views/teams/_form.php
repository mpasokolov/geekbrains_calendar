<?php

use common\models\Teams;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin() ?>

<?php

    $users = User::find() -> asArray() -> all();
    $userItems = ArrayHelper::map($users, 'id', 'username');
    $userParams = [
        'prompt' => 'Выберите пользователя',
    ];

    $teams = Teams::find() -> where(['!=', 'id', $model -> id]) -> asArray() -> all();
    $teamsItems = ArrayHelper::map($teams, 'id', 'name');
    $teamsParams = [
        'prompt' => 'Выберите родительские проект',
    ]

?>

<?= $form -> field($model, 'name') -> textInput() ?>

<?= $form -> field($model, 'teamlead') -> dropDownList($userItems, $userParams) ?>

<?= $form -> field($model, 'parent_id') -> dropDownList($teamsItems, $teamsParams) ?>

<?= Html::submitButton('Создать команду', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>
