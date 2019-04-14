<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>

<?php if(Yii::$app->session->hasFlash('fail')): ?>
    <div class="alert alert-danger" role="alert">
        <?= Yii::$app->session->getFlash('fail') ?>
    </div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('already-sent')): ?>
    <div class="alert alert-danger" role="alert">
        <?= Yii::$app->session->getFlash('already-sent') ?>
    </div>
<?php endif; ?>

<?php $team -> parentTeam
    ? $message = \yii\helpers\Html::a($team -> parentTeam -> name, Url::to(['view', 'id' => $team -> parent_id]))
    : $message = 'Не задан'
?>
<h3>Родительский проект: <?= $message ?></h3>

<?= GridView::widget([
    'dataProvider' => $teamsDataProvider,
    'summary' => '<h3>Состав команды</h3>',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'username',
            'label' => 'Имя пользователя',
            'value' => 'users.username',

        ],
        [
            'attribute' => 'email',
            'label' => 'Email адрес',
            'value' => 'users.email',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'visibleButtons' => [
                'delete' => \Yii::$app -> user -> can('deleteUserFromTeam'),
            ],
            'urlCreator' => function ($action, $model, $key, $index) use ($teamId){
                return Url::to(['teams/'. $action, 'id' => $model -> id, 'team' => $teamId]);
            },
        ],

    ]
]) ?>

<?php if (\Yii::$app -> user -> can('disbandTeam')): ?>

    <a class="btn btn-danger" href="<?= Url::to(['teams/delete-all', 'team' => $teamId]); ?>">Расформировать команду</a><br><br>

<?php endif; ?>

<?php if (isset($users)): ?>

    <div class="send-invite col-sm-3">
        <?php $form = ActiveForm::begin();

            $users = ArrayHelper::map($users, 'id', 'username');
            $params = [
                'prompt' => 'Выберите пользователя'
            ]
        ?>

        <?= $form -> field($invites, 'id_to') -> dropDownList($users, $params); ?>
        <?= $form -> field($invites, 'id_team') -> hiddenInput(['value' => $teamId]) -> label(false); ?>

        <?= Html::submitButton('Отправить приглашение ', ['class' => 'btn btn-success']) ?>

        <?php ActiveForm::end() ?>
    </div>

<?php endif; ?>

<?php if (isset($tasksDataProvider) && isset($tasksSearchModel)): ?>

    <?php Pjax::begin(); ?>
    <div class="task-list">
        <?= GridView::widget([
            'dataProvider' => $tasksDataProvider,
            'filterModel' => $tasksSearchModel,
            'summary' => '<h3>Список задач команды</h3>',
            'columns' => [
                'name:text:Название',
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
                    'attribute' => 'deadline',
                    'format' => ['date', 'php:Y-m-d'],

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
                    'attribute' => 'finish_time',
                    'label' => 'Дата выполнения',
                    'value' => function($model) {
                        return $model -> finish ? date('Y-m-d', $model -> finish_time) : 'Не завершена';
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Дата создания',
                    'format' => ['date', 'php:Y-m-d'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'urlCreator' => function ($action, $model, $key, $index) use($teamId) {
                        return Url::to(['tasks/'. $action, 'id' => $model->id, 'task' => $teamId]);
                    },
                    'template' => '{view} {update} {delete} {finish}',
                    'buttons' => [
                        'finish' => function ($url, $model, $key) {
                            if (!$model -> finish) {
                                return Html::a('', $url, ['class' => 'glyphicon glyphicon-ok']);
                            }
                        },
                        'update' => function ($url, $model, $key) {
                            if (!$model -> finish) {
                                return Html::a('', $url,
                                    [
                                        'class' => 'glyphicon glyphicon-pencil',
                                        'data-pjax' => 0
                                    ]
                                );
                            }
                        },
                        'delete' => function ($url, $model, $key) use($teamId){
                            if (!$model -> finish) {
                                return Html::a('',
                                    Url::to(['teams/delete-task', 'id' => $model->id, 'task' => $teamId]),
                                    [
                                        'class' => 'glyphicon glyphicon-trash',
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-pjax' => 1
                                    ]
                                );
                            }
                        },
                    ]
                ]
            ]
        ]) ?>
    </div>
    <?php Pjax::end(); ?>

<?php endif; ?>
