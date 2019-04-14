<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>

<?php
$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);

?>
<?= $form -> field($model, 'extra') -> radioList(
        ['1' => 'Закрытые задачи за последнюю неделю','2' => 'Просроченые задачи']); ?>

<div class="form-group">
    <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-danger']) ?>
    <?= Html::a('Создать задачу', '/../tasks/create', ['class' => 'btn btn-success']) ?>

</div>

<?php ActiveForm::end(); ?>