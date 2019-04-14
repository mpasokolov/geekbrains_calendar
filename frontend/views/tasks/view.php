<?php

use frontend\assets\TaskViewAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

TaskViewAsset::register($this);

$this -> title = 'Описание задачи';
$this -> params['breadcrumbs'][] = ['label' => 'Tasks', 'url' => 'index'];
$this -> params['breadcrumbs'][] = ['label' => $model -> id, 'url' => ['index', 'id' => $model -> id]];
$this -> params['breadcrumbs'][] = $this -> title;
?>

<p>
    <?php if (\Yii::$app -> user -> can('adminAccess')): ?>
        <?= Html::a('К списку задач', '@web/admin/tasks/index', ['class' => 'btn btn-success']) ?>
    <?php else: ?>
        <?= Html::a('К списку задач', 'index', ['class' => 'btn btn-success']) ?>
    <?php endif; ?>

    <?php if (\Yii::$app -> user -> can('updateTask') && $model -> finish == 0): ?>
        <?= Html::a('Редактировать задачу', ['update', 'id' => $model -> id], ['class' => 'btn btn-success']) ?>
    <?php endif; ?>
</p>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'name',
        'description',
        'deadline:datetime',
        [
            'attribute' => 'finish_time',
            'label' => 'Дата завершения',
            'value' => function($model) {
                return date('M j, Y, g:i A', $model -> finish_time) ?? 'Не завершена';
            }
        ],
        [
            'attribute' => 'id_team',
            'label' => 'Имя команды',
            'value' => $model -> teams -> name,
        ],
        [
            'attribute' => 'id_user',
            'label' => 'Исполнитель',
            'value' => $model -> users -> username,
        ],
        [
            'attribute' => 'id_admin',
            'label' => 'Автор',
            'value' => $model -> admins -> username,
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Дата создания',
            'format' => 'datetime'
        ],
        [
            'attribute' => 'updated_at',
            'label' => 'Дата обновления',
            'format' => 'datetime'
        ],
        [
            'attribute' => 'report',
            'label' => 'Отчет о задаче',
            'value' => function($model) {
                return $model -> report ? $model -> report : 'Отчет будет доступен после завершения задачи';
            }
        ]
    ]
]) ?>

<div class="container" id="chat">
</div>

