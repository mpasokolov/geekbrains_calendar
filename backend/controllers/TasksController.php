<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-16
 * Time: 20:25
 */

namespace backend\controllers;
use common\models\SearchTasksByAdmin;
use common\models\TasksSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;

class TasksController extends Controller {
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex() {
        $searchModel = new TasksSearch();
        $searchModel -> setStrategy(new SearchTasksByAdmin());
        $dataProvider = $searchModel -> searchTasks(\Yii::$app -> request -> queryParams);

        Url::remember();

        return $this -> render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }
}