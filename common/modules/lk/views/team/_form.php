<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html; ?>

<div class="team_form">
    <?php $form = ActiveForm::begin() ?>

    <div class="col-sm-3">
        <?= $form -> field($team, 'name') -> textInput() ?>
        <?= Html::submitButton('Создать команду', ['url' => 'team/create', 'class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>