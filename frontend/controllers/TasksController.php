<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-09
 * Time: 20:00
 */

namespace frontend\controllers;

use common\models\SearchTasksByTeamLead;
use common\models\SearchTasksByUser;
use common\models\Tasks;
use common\models\TasksSearch;
use common\models\UsersTeams;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

class TasksController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'teamLead', 'user'],
                        'actions' => ['index', 'finish', 'view']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['teamLead', 'admin'],
                        'actions' => ['create', 'update', 'delete', 'get-users-by-team-name']
                    ],
                ]
            ]
        ];
    }

    public function actionIndex() {
        $searchModel = new TasksSearch();
        $dataProvider = null;

        if (\Yii::$app -> user -> can('adminAccess')) {
            return $this -> redirect('@web/admin/tasks');
        }

        $isTeamLead = key(\Yii::$app -> authManager -> getRolesByUser(\Yii::$app -> user -> id)) === 'teamLead';

        if ($isTeamLead) {
            $searchModel -> setStrategy(new SearchTasksByTeamLead());
            $dataProvider = $searchModel -> searchTasks(\Yii::$app -> request -> queryParams);
        } else {
            $searchModel -> setStrategy(new SearchTasksByUser());
            $dataProvider = $searchModel -> searchTasks(\Yii::$app -> request -> queryParams);
        }

        Url::remember();

        return $this -> render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate() {
        $task = new Tasks(['scenario' => Tasks::SCENARIO_CREATE]);
        if ($task -> load(\Yii::$app -> request -> post()) && $task -> save()) {
            $message = 'Задача успешно создана!' . '<a href="' . Url::to('index') . '"> К списку задач</a>';
            \Yii::$app -> session -> setFlash('success', $message);
            return $this -> refresh();
        }

        return $this -> render('create', ['model' => $task]);
    }

    public function actionUpdate($id) {
        $model = $this -> findModel($id);

        if (!Tasks::checkAccess($model) && !\Yii::$app -> user -> can('updateTask')) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        if ($model -> finish == 1) {
            throw new MethodNotAllowedHttpException('Нельзя редактировать завершенную задачу!');
        } else {
            $model -> id_team = null;
        }

        if ($model -> load(\Yii::$app -> request -> post()) && $model -> save()) {
            return $this -> render('update', ['model' => $model, 'result' => 'Задача успешно обновлена']);
        }

        return $this -> render('update', ['model' => $model]);
    }

    public function actionDelete($id) {
        $model = $this -> findModel($id);

        if (!Tasks::checkAccess($model) && !\Yii::$app -> user -> can('deleteTask')) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        $model -> delete();

        \Yii::$app -> session -> setFlash('success', 'Задача успешно удалена');

        return $this -> goBack(\Yii::$app -> request -> referrer);
    }

    public function actionView($id) {
        $model = $this -> findModel($id);

        if (!Tasks::checkAccess($model) && !\Yii::$app -> user -> can('viewTask')) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        return $this -> render('view', ['model' => $model]);
    }

    public function actionFinish($id) {
        $model = $this -> findModel($id);
        $model->scenario = Tasks::SCENARIO_FINISH;

        if (!Tasks::checkAccess($model) && !\Yii::$app -> user -> can('finishTask')) {
            throw new MethodNotAllowedHttpException('У вас нет прав для данного действия');
        }

        if ($model -> load(\Yii::$app -> request -> post()) && $model -> save()) {
            \Yii::$app -> session -> setFlash('success', 'Задача успешно завершена!');
            return $this->goBack(\Yii::$app->request->referrer);
        }

        return $this -> render('finish', ['model' => $model]);
    }

    public function actionGetUsersByTeamName() {
        if(\Yii::$app -> request -> isAjax){
            $id = (int)\Yii::$app -> request->post('id');

            $option = '';
            $users = UsersTeams::find()
                -> where('id_team=:id',[':id' => $id])
                -> joinWith('users')
                -> all();

            foreach ($users as $user){
                $option .= '<option value="' . $user -> users -> id . '">' . $user -> users -> username . '</option>';
            }

            return $option;
        }
    }

    /**
     * @param $id
     * @return Tasks|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id) {
        if (($model = Tasks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Данной страницы не существует');
    }
}