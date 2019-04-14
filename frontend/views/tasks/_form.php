<?php

use common\models\Teams;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="tasks-form">
    <?php Pjax::begin(); ?>

    <?php if (isset($result)): ?>
        <div class="alert-success alert fade in"><?= $result?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'options' => [
            'data-pjax' => true,
        ]
    ])
    ?>

    <?= $form -> field($model, 'name') -> textInput() ?>

    <?php
        if (\Yii::$app -> user -> can('adminPermission')) {
            $teams = Teams::find() -> all();
        } else {
            $teams = Teams::find() -> where(['teamlead' => \Yii::$app -> user -> id]) -> all();
        }
        $items = ArrayHelper::map($teams, 'id', 'name');
        $params = [
            'prompt' => 'Выберите команду',
            'site' => '',
            'onchange' => '
                 $.post(
                  "'. Url::toRoute('tasks/get-users-by-team-name') .'",
                  {id: $(this).val()},
                  function(data){
                    $("select#teams").html(data).attr("disabled", false)
                  }
                 )
             '
        ]
    ?>

    <?= $form -> field($model, 'id_team') -> dropDownList($items, $params) ?>
    <?php
        $users = User::find()  -> all();
        $items = ArrayHelper::map($users, 'id', 'username');
        $params = [
            'prompt' => 'Выберите юзера',
            'id' => 'teams',
            'disabled' => $model -> id_team === null ? true : false,

        ]
    ?>

    <?= $form -> field($model, 'id_user') -> dropDownList($items, $params) ?>

    <?= $form -> field($model, 'deadline') -> textInput([
            'type' => 'date',
            'value' => gettype($model -> deadline) === 'integer'
                ? date('Y-m-d', $model -> deadline)
                : ''
        ]) ?>
    <?= $form -> field($model, 'description') -> textarea() ?>
    <?= $form -> field($model, 'id_admin') -> hiddenInput(['value' => \Yii::$app -> user -> id]) -> label(false); ?>

    <?= Html::submitButton($this -> title === 'Редактирование задачи'
        ? 'Обновить задачу'
        : 'Создать задачу',
        ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>
    <?php Pjax::end(); ?>
</div>
