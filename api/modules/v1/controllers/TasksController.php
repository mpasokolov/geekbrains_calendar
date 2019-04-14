<?php

namespace api\modules\v1\controllers;

use common\models\Tasks;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class TasksController extends ActiveController {
    public $modelClass = Tasks::class;

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['apiAuth'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                [
                    'class' => HttpBasicAuth::class,
                    'auth' => function ($username, $password) {
                        $user = User::findByUsername($username);
                        if ($user !== null && $user -> validatePassword($password)) {
                            return $user;
                        }

                        return null;
                    }
                ],
                [
                    'class' => HttpBearerAuth::class,
                ],
            ]
        ];

        return $behaviors;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex() {
        $filter = \Yii::$app -> request -> get('filter');

        $query = Tasks::find()
            -> alias('t')
            -> joinWith('admins a')
            -> joinWith('users u');

        if (array_key_exists('date', $filter)) {
            $interval = $this -> getMonthInterval($filter['date']);
            $query -> andFilterWhere(['between', 't.created_at', $interval['start'], $interval['end']]);
        }

        if (array_key_exists('admin', $filter)) {
            $query -> andFilterWhere(['like', 'a.username', $filter['admin']]);
        }

        if (array_key_exists('user', $filter)) {
            $query -> andFilterWhere(['like', 'u.username', $filter['user']]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getMonthInterval($date) {
        $dateArr = explode('-', $date);
        $daysInMonth =  date('t', mktime(0, 0, 0, $dateArr[1], 1, $dateArr[0]));
        $startDay = mktime(0, 0, 0, $dateArr[1], 1, $dateArr[0]);
        $endDay = mktime(0, 0, 0, $dateArr[1], $daysInMonth, $dateArr[0]);
        return ['start' => $startDay, 'end' => $endDay];
    }


}