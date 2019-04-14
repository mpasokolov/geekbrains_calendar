<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-08
 * Time: 22:36
 */

namespace frontend\controllers;


use common\models\History;
use common\models\Messages;
use yii\web\Controller;

class ChatController extends Controller {

    public function beforeAction($action) {
        $this -> enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionGetData() {
        if (\Yii::$app->request->isAjax) {
            $data = \Yii::$app->request->post();
            $messages = Messages::find()-> where([
                'id_task' => $data['task'],
            ]) -> joinWith('users')-> asArray() -> all();

            $userName = \Yii::$app -> user -> identity;
            $userId = \Yii::$app -> user -> id;

            $history = History::findOne(['id_user' => $userId, 'id_task' => $data['task']]);

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [
                'messages' => $messages,
                'user' => $userName,
                'lastViewMsg' => $history['id_message'],
            ];
        }
    }

    public function actionAddMessage() {
        if (\Yii::$app -> request -> isAjax) {
            $data = \Yii::$app->request->post();
            $message = new Messages();
            $message -> message = $data['message'];
            $message -> id_user = $data['userId'];
            $message -> id_task = $data['task'];
            $message -> time = $data['time'];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($message -> save()) {
                return [
                    'status' => 1,
                    'id' => $message -> id,
                ];
            }

            return [
                'status' => 0,
            ];
        }
    }

    public function actionAddHistory() {
        if (\Yii::$app -> request -> isAjax) {
            $data = \Yii::$app -> request -> post();
            $history = History::findOne(['id_task' => $data['id_task'], 'id_user' => $data['id_user']]);
            if ($history) {
                $history -> id_message = $data['id_message'];
                $history -> save();
            } else {
                $history = new History();
                $history -> id_user = $data['id_user'];
                $history -> id_task = $data['id_task'];
                $history -> id_message = $data['id_message'];
                $history -> save();
            }
        }

    }
}