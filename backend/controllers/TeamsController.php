<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-10
 * Time: 17:33
 */

namespace backend\controllers;

use common\models\Tasks;
use common\models\Teams;
use common\models\TeamsSearch;
use common\models\User;
use common\models\UsersTeams;
use Facebook\WebDriver\Net\URLChecker;
use yii\base\Event;
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
                        'roles' => ['admin'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex() {
        $teamsSearchModel = new TeamsSearch();
        $teamsDataProvider = $teamsSearchModel -> searchAllTeams(\Yii::$app -> request -> queryParams);

        return $this -> render('index', [
            'searchModel' => $teamsSearchModel,
            'dataProvider' => $teamsDataProvider,
        ]);
    }

    public function actionCreate() {
        $model = new Teams();
        if ($model -> load(\Yii::$app -> request -> post()) && $model -> save()) {
            $model -> trigger(Teams::EVENT_CREATE_TEAM);
            $message = 'Команда успешно создана!' . '<a href="' . Url ::to('index') . '"> К списку команд</a>';
            \Yii ::$app -> session -> setFlash('success', $message);
            return $this -> refresh();
        }

        return $this -> render('create', ['model' => $model]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model -> load(\Yii::$app -> request -> post()) && $model -> save()) {
            $message = 'Команда успешно обновлена!' . '<a href="' . Url::to('index') . '"> К списку команд</a>';
            \Yii::$app -> session -> setFlash('success', $message);
            return $this -> refresh();
        }

        return $this -> render('update', ['model' => $model]);
    }

    public function actionDelete($id) {
        $model = $this -> findModel($id);

        if ($model -> delete()) {
            $message = 'Команда успешно удалена!';
            \Yii::$app -> session -> setFlash('success', $message);
            return $this -> redirect(Url::to('index'));
        }
    }

    private function findModel($id) {
        if (($model = Teams::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Данной страницы не существует');
    }
}