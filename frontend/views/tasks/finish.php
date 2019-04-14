<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

?>

<h4>Завершение задачи <?= $model -> name ?></h4>
<p>Для завершения задачи напишите небольшой отчет о ее выполнении:</p>

<div class="finish-task">
    <?php $form = ActiveForm::begin() ?>

    <?= $form -> field($model, 'report') -> textarea() ?>

    <?= Html::submitButton('Завершить задачу', ['class' => 'btn btn-success']) ?>

    <?php if (key(\Yii::$app -> authManager -> getRolesByUser(\Yii::$app -> user -> id)) === 'admin'): ?>
        <?= Html::a('К списку задач', '@web/admin/tasks/index', ['class' => 'btn btn-danger']) ?>
    <?php else: ?>
        <?= Html::a('К списку задач', 'index', ['class' => 'btn btn-danger']) ?>
    <?php endif; ?>

    <?php ActiveForm::end() ?>
</div>
