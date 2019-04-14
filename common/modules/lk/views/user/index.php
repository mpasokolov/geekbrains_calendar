<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

?>

<h1>Личный кабинет</h1>

<h3>Ваши данные:</h3>

<?= DetailView::widget([
    'model' => $user,
    'attributes' => [
        'username',
        'email'
    ]
]); ?>

<?= Html::a('Изменить данные', ['update', 'id' => $user -> id], ['class' => 'btn btn-success']); ?>
<br>

<?php Pjax::begin(['enablePushState' => false]); ?>
<?= GridView::widget([
    'dataProvider' => $invitesDataProvider,
    'showOnEmpty' => false,
    'emptyText' => '<h3>У вас нет приглашений в команду.</h3>',
    'summary' => '<h3>Ваши приглашения:</h3>',
    'columns' => [
        [
            'attribute' => 'username',
            'value' => 'reporterUser.username',
            'label' => 'Имя отправителя',
            'format' => 'raw'
        ],
        [
            'attribute' => 'name',
            'value' => 'team.name',
            'label' => 'Название команды',
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{accept} {reject} ',
            'buttons' => [
                'accept' => function ($url,$model,$key) {
                    return Html::a('Принять',
                        Url::to(
                            ['accept', 'invite' => $model -> id]),
                            [
                                'class' => 'btn btn-success btn-xs',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            ]
                        );
                },
                'reject' => function ($url,$model,$key) {
                    return Html::a('Отклонить',
                        Url::to(['reject', 'id' => $model -> id]),
                            [
                                'class' => 'btn btn-danger btn-xs',
                            ]
                        );
                },
            ],
        ]
    ],
    ])
?>
<?php Pjax::end(); ?>

<?= GridView::widget([
        'dataProvider' => $teamsDataProvider,
        'summary' => '<h3>Ваши команды:</h3>',
        'showOnEmpty' => false,
        'emptyText' => '<h3>Вы не состоите ни в одной команде.</h3>',
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Название команды',
                'value' => 'teams.name'
            ],
            [
                'attribute' => 'teamlead',
                'label' => 'TeamLead',
                'value' => 'teams.teamLead.username'
            ],
            /*
             * function($model) {
                    $items = [];
                    foreach ($model -> teams as $team) {
                        $items[] = $team -> name;
                    }
                    return implode('\n', $items);
                }
             */
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to(['/../teams/'. $action, 'id' => $model -> teams -> id]);
                },
            ],
        ]
    ])
?>


