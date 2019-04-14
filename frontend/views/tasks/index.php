<?php

use app\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="tasks">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => 'Нет доступных задач',
        'layout'=>"<h1>{summary}</h1>\n{items}\n{pager}",
        'summary' =>'Список задач',
        'rowOptions' => function ($model) {
            $date = getdate();

            if ($model -> finish === 1) {
                return ['class' => 'success'];
            };

            if ($model -> deadline < mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year'])) {
                return ['class' => 'danger'];
            };
        },
        'columns' => [
            'name:text:Название',
            'description:text:Описание',
            [
                'attribute' => 'id_admin',
                'label' => 'Автор',
                'value' => 'admins.username',

            ],
            [
                'attribute' => 'id_user',
                'label' => 'Исполнитель',
                'value' => 'users.username',

            ],
            [
                'attribute' => 'id_team',
                'label' => 'Команда',
                'value' => 'teams.name',

            ],
            'deadline:date',
            [
                'attribute' => 'finish',
                'label' => 'Cтатус задачи',
                'value' => function($model) {
                    return $model -> finish === 1 ? 'Завершена' : ' В работе';
                }
            ],
            [
                'attribute' => 'finish_time',
                'label' => 'Дата выполнения',
                'value' => function($model) {
                    return $model -> finish ? date('M j, Y', $model -> finish_time) : 'Не завершена';
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Дата создания',
                'format' => 'date',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to(['tasks/'. $action, 'id' => $model->id]);
                },
                'template' => '{view} {update} {delete} {finish}',
                'visibleButtons' => [
                    'update' => \Yii::$app->user->can('updateTask'),
                    'delete' => \Yii::$app->user->can('deleteTask'),
                    'view' => \Yii::$app->user->can('viewTask'),
                    'finish' => \Yii::$app->user->can('finishTask')
                ],
                'buttons' => [
                    'finish' => function ($url, $model, $key) {
                        if (!$model -> finish) {
                            return Html::a('', $url, ['class' => 'glyphicon glyphicon-ok']);
                        }
                    },
                    'update' => function ($url, $model, $key) {
                        if (!$model -> finish) {
                            return Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil']);
                        }
                    },
                    'delete' => function ($url, $model, $key) {
                        if (!$model -> finish) {
                            return Html::a('', $url, ['class' => 'glyphicon glyphicon-trash']);
                        }
                    },
                ]
            ]
        ]
    ]) ?>

    <?php if (\Yii::$app -> user ->can('createTask')): ?>
        <p><?= Html::a('Создать задачу', 'create', ['class' => 'btn btn-success']) ?></p>
    <?php endif; ?>
</div>