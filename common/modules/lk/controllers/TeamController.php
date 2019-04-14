<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-11
 * Time: 19:05
 */

namespace common\modules\lk\controllers;


use common\models\Invites;
use common\models\Tasks;
use common\models\Teams;
use common\models\TeamsSearch;
use common\models\User;
use common\models\UsersTeams;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeamController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['teamLead', 'admin'],
                        'actions' => ['delete', 'delete-all'],
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

        $users = User::find() -> where(['t.id_team' => null]) -> joinWith('teams t') -> asArray() -> all();

        $teamsSearchModel = new TeamsSearch();
        $teamsDataProvider = $teamsSearchModel -> searchUsersByTeam(\Yii::$app -> request ->queryParams, $id);

        return $this -> render('view',
            [
                'users' => $users,
                'invites' => $invites,
                'teamsDataProvider' => $teamsDataProvider,
                'team' => $id]);
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
                $user = UsersTeams::findOne(['id_user' => $id, 'id_team' => $team]);

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

        return $this -> redirect(Url::to(['team/view', 'id' => $team]));
    }


    public function actionDeleteAll($team) {
        if (!Teams ::checkAccess($team)) {
            throw new NotFoundHttpException('У вас нет доступа для данного действия');
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

        return $this -> redirect(Url ::to(['team/view', 'id' => $team]));
    }
}
