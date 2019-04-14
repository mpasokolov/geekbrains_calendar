<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php \yii\widgets\Pjax::begin(); ?>
<?= $this -> render('_search', ['model' => $searchModel]) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout'=>"<h1>{summary}</h1>\n{items}\n{pager}",
    'summary' =>'Список задач',
    'rowOptions' => function ($model) {
        $date = getdate();

        if ($model -> finish === 1) {
            return ['class' => 'success'];
        };

        if ($model -> deadline < mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']) + 1) {
            return ['class' => 'danger'];
        };
    },
    'columns' => [
        'id',
        'name:text:Название',
        'description:text:Описание',
        [
            'attribute' => 'id_admin',
            'value' => 'admins.username',
            'label' => 'Автор'
        ],
        [
            'attribute' => 'id_user',
            'value' => 'users.username',
            'label' => 'Исполнитель'
        ],
        [
            'attribute' => 'id_team',
            'value' => 'teams.name',
            'label' => 'Команда'
        ],
        [
            'attribute' => 'deadline',
            'format' => ['date', 'php:Y-m-d'],
            'label' => 'Срок исполнения'
        ],
        [
            'attribute' => 'finish_time',
            'label' => 'Дата завершения',
            'value' => function($model) {
                return $model -> finish === 1 ? date('Y-m-d', $model -> finish_time) : 'Не завершена';
            }
        ],
        [
            'attribute' => 'finish',
            'value' => function($model) {
                return $model -> finish === 1 ? 'Выполнена' : 'Не выполнена';
            },
            'filter' => ['1' => 'Выполнена', '0' => 'Не выполнена'],
            'label' => 'Статус',
        ],
        [
            'class' => '\yii\grid\ActionColumn',
            'urlCreator' => function ($action, $model, $key, $index) {
                return Url::to(['../tasks/'. $action, 'id' => $model->id]);
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
                'update' => function($url, $model, $key) {
                    if ($model -> finish == 1) {
                        return null;
                    } else {
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil']);
                    }
                }
            ]
        ]
    ]
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
