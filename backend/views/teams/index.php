<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?= Html::a('Создать команду','create' , ['class' => 'btn btn-success']); ?>

<?= GridView::widget([
    'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'layout'=>"<h1>{summary}</h1>\n{items}\n{pager}",
    'summary' =>'Список команд',
    'columns' => [
        'id:integer:ID команды',
        [
            'attribute' => 'name',
            'label' => 'Название команды',
            'value' => function ($model) {
                return Html::a($model -> name, Url::to(['/../teams/view',  'id' => $model -> id]));
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'teamlead',
            'label' => 'Лидер команды',
            'value' => 'teamLead.username'
        ],
        [
            'attribute' => 'parent_id',
            'label' => 'Родительский проект',
            'format' => 'html',
            'value' => function($model) {
                return !$model -> parent_id ? 'Не задан' : $model -> parentTeam -> name ;
            }
        ],
        [
            'attribute' => 'created_at',
            'format' => ['date', 'php:Y-m-d'],
            'label' => 'Время создания'
        ],
        [
            'attribute' => 'updated_at',
            'label' => 'Время изменения',
            'format' => ['date', 'php:Y-m-d'],
        ],
        [
            'attribute' => 'status',
            'value' => function($model) {
                return $model -> status === 1 ? 'Активна' : 'Не активна';
            },
            'filter' => ['1' => 'Активна', '0' => 'Не активна'],
            'label' => 'Статус команды',
        ],
        [
            'class' => '\yii\grid\ActionColumn',
            'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'view') {
                    return Url::to(['/../teams/'. $action, 'id' => $model->id]);
                } else {
                    return Url::to(['/teams/'. $action, 'id' => $model->id]);
                }
            },
        ]

    ]
]);
?>
