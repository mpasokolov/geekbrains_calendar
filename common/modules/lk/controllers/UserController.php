<?php
namespace common\modules\lk\controllers;


use common\models\Invites;
use common\models\InvitesSerach;
use common\models\Tasks;
use common\models\TeamsSearch;
use common\models\User;
use common\models\UsersTeams;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;

class UserController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex() {
        return $this -> renderIndex();
    }

    public function actionAccept($invite) {
        $invite = Invites::findOne($invite);
        if (!$invite) {
            throw new ErrorException('Ошибка при принятии приглашения');
        }

        if ($invite -> id_to !== \Yii::$app -> user -> id) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        $transaction = UsersTeams::getDb() -> beginTransaction();

        try {
            $user = new UsersTeams();
            $user -> id_team = $invite -> id_team;
            $user -> id_user = \Yii::$app -> user -> id;

            $user -> save();

            $currentRole =  key(\Yii::$app -> authManager -> getRolesByUser(\Yii::$app -> user -> id));

            if ($currentRole === 'guest') {
                $userRole = \Yii::$app -> authManager -> getRole('user');
                $guestRole = \Yii::$app -> authManager -> getRole('guest');
                \Yii::$app -> authManager -> revoke($guestRole, \Yii::$app -> user -> id);
                \Yii::$app -> authManager -> assign($userRole, \Yii::$app -> user -> id);
            }

            $invite -> delete();

            $transaction -> commit();
            \Yii::$app -> session -> setFlash('success', 'Приглашение принято!');
        } catch (\Throwable $e) {
            $transaction -> rollBack();
            throw $e;
        }

        if (\Yii::$app -> request->isAjax) {
            return $this -> renderIndex();
        } else {
            return $this -> redirect(['index']);
        }
    }

    public function actionUpdate($id) {
        $user = $this -> findModel($id);

        if ($user -> id !== \Yii::$app -> user -> id) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        if ($user -> load(\Yii::$app -> request -> post()) && $user -> save()) {
            \Yii::$app -> session -> setFlash('success', 'Данные успешно обновлены');
            return $this -> redirect('index');
        }
        return $this -> render('update', ['user' => $user]);
    }

    public function actionReject($id) {
        Invites::findOne($id) -> delete();

        if (\Yii::$app->request->isAjax) {
            return $this -> renderIndex();
        } else {
            return $this -> redirect(['index']);
        }
    }

    protected function renderIndex() {
        $userId = \Yii::$app -> user -> id;

        $user = $this -> findModel($userId);

        $teamsSearchModel = new TeamsSearch();
        $teamsDataProvider = $teamsSearchModel -> searchTeamsByUser(\Yii::$app -> request -> queryParams);

        $inviteSearchModel = new InvitesSerach();;
        $invitesDataProvider = $inviteSearchModel -> search(\Yii::$app -> request -> queryParams);

        return $this -> render('index', [
            'teamsSearchModel' => $teamsSearchModel,
            'teamsDataProvider' => $teamsDataProvider,
            'invitesDataProvider' => $invitesDataProvider,
            'user' => $user
        ]);
    }

    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Данной страницы не существует');
    }
}