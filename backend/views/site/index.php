<?php

use yii\bootstrap\Html;

?>

<div class="admin-default-index">
    <h2>Добро пожаловать в панель администратора!</h2>
    <h3>Доступные действия: </h3>
    <div class="list-group">
        <?= Html::a('Управление задачами', '@web/tasks/index',
            ['class' => 'list-group-item list-group-item-action list-group-item-secondary'])
        ?>
        <?= Html::a('Управление проектами', '@web/teams/index',
            ['class' => 'list-group-item list-group-item-action list-group-item-secondary'])
        ?>
    </div>
</div>
