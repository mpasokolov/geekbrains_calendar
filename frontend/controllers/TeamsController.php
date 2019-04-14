<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-10
 * Time: 18:53
 */

namespace frontend\controllers;


use common\models\Invites;
use common\models\Tasks;
use common\models\TasksSearch;
use common\models\Teams;
use common\models\TeamsSearch;
use common\models\User;
use common\models\UsersTeams;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeamsController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['teamLead', 'admin'],
                        'actions' => ['delete', 'delete-all', 'delete-task'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['teamLead', 'admin', 'user'],
                        'actions' => ['view']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['guest']
                    ],
                ],
            ]
        ];
    }

    public function actionView($id) {
        return $this -> renderView($id);
    }

    public function actionDelete($id, $team) {
        if (!Teams::checkAccess($team)) {
            throw new NotFoundHttpException('У вас нет доступа для просмотра данной страницы');
        }

        $teamLeadId = Teams::getTeamLeadId($team);

        if ($teamLeadId === (int)$id) {
            \Yii::$app -> session -> setFlash('fail', 'Нельзя удалить лидера команды', false);
        } else {
            $transaction = \Yii::$app -> db -> beginTransaction();

            try {
                $user = UsersTeams::findOne(['id' => $id, 'id_team' => $team]);

                if (!$user) {
                    throw new ErrorException('Ошибка при удалении члена команды');
                }

                Tasks::updateAll(
                    ['id_user' => \Yii::$app -> user -> id],
                    ['id_team' => $user -> id_team, 'id_user' => $user -> id_user]);

                $user -> delete();

                $transaction -> commit();
                \Yii::$app -> session -> setFlash('success', 'Пользователь успешно удален из команды', false);
            } catch(\Throwable $e) {
                $transaction -> rollBack();
                throw $e;
            }
        }

        return $this -> redirect(Url::to(['teams/view', 'id' => $team]));
    }


    public function actionDeleteAll($team) {
        if (!Teams::checkAccess($team)) {
            throw new NotFoundHttpException('У вас нет доступа для просмотра данной страницы');
        }

        $transaction = \Yii::$app -> db ->beginTransaction();

        try {
            $teamLeadId = Teams::getTeamLeadId($team);

            $users = UsersTeams::find() -> where('id_team = :id_team and id_user != :id_user',
                ['id_team' => $team, 'id_user' => $teamLeadId]) -> all();

            foreach ($users as $user) {
                Tasks::updateAll(
                    ['id_user' => \Yii::$app -> user -> id],
                    ['id_team' => $user -> id_team, 'id_user' => $user -> id_user]
                );

                $user -> delete();
            }
            $transaction -> commit();

            \Yii::$app -> session -> setFlash('success', 'Команда успешно расформирована', false);
        } catch(\Throwable $e) {
            $transaction -> rollBack();
            throw $e;
        }

        return $this -> redirect(Url ::to(['teams/view', 'id' => $team]));
    }

    protected function renderView($id) {
        $invites = new Invites();

        if ($invites -> load(\Yii::$app -> request ->post()) && $invites -> validate()) {
            $invite = Invites::findOne([
                'id_from' => \Yii::$app -> user -> id,
                'id_to' => $invites -> id_to,
                'id_team' => $invites -> id_team
            ]);

            if (!empty($invite)) {
                \Yii::$app -> session -> setFlash('already-sent', 'Приглашение уже было отправлено ранее!');
            } else {
                $invites -> save(false);
                \Yii::$app -> session -> setFlash('success', 'Приглашение успешно отправлено!');
            }

            return $this -> refresh();
        }

        $team = Teams::find() -> where(['teams.id' => $id]) -> joinWith('parentTeam pt') -> one();

        $teamsSearchModel = new TeamsSearch();
        $teamsDataProvider = $teamsSearchModel -> searchUsersByTeam(\Yii::$app -> request -> queryParams, $id);

        $data = [
            'team' => $team,
            'teamId' => $id,
            'invites' => $invites,
            'teamsDataProvider' => $teamsDataProvider,
        ];

        if (\Yii::$app -> user -> can('inviteInTeam')) {
            $users = User::find() -> where(['t.id_team' => null]) -> joinWith('teams t') -> asArray() -> all();

            $data['users'] = $users;
        }

        if (\Yii::$app -> user -> can('manageTeamTasks')) {
            $tasksSearchModel = new TasksSearch();
            $tasksDataProvider = $tasksSearchModel -> searchByTeam(\Yii::$app -> request -> queryParams, $id);

            $data['tasksSearchModel'] = $tasksSearchModel;
            $data['tasksDataProvider'] = $tasksDataProvider;
        }

        return $this -> render('view', $data);
    }

    public function actionDeleteTask($id, $task) {
        $model = Tasks::findOne($id);

        if (!Tasks::checkAccess($model)) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        $model -> delete();

        \Yii::$app -> session -> setFlash('success', 'Задача успешно удалена');

        if (\Yii::$app->request->isAjax) {
            return $this -> renderView($task);
        } else {
            return $this -> goBack();
        }
    }

    protected function findModel($id) {
        if (($model = Teams::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Данной страницы не существует');
    }
}