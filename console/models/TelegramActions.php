<?php

namespace console\models;

use common\models\Tasks;
use common\models\Teams;
use common\models\TelegramAuth;
use common\models\TelegramSubscribe;
use common\models\User;
use TelegramBot\Api\Types\Message;
use yii\base\Model;

class TelegramActions extends Model {
    public function help() {
        $response = "Доступные команды \n";
        $response .= "/help - список команд\n";
        $response .= "/sp_create - подписка на создание проектов\n";
        $response .= "/login ##username ##password  подписка на создание проектов\n";
        $response .= "/task_create ##name ##team ##user ##deadline ##description - создание задачи\n";

        return $response;
    }

    public function sp_create(Message $message) {
        if (TelegramSubscribe::findOne([
            'channel' => 'project_create',
            'telegram_chat_id' => $message -> getFrom() -> getId()
        ])) {
            return 'Вы уже подписаны на рассылку по новым проектам';
        }

        $model = new TelegramSubscribe([
            'channel' => 'project_create',
            'telegram_chat_id' => $message -> getFrom() -> getId(),
        ]);

        if ($model -> save()) {
            $response = 'Вы подписаны на уведомления по созданию проектов';
        } else {
            $response = 'При подписке произошла ошибка';
        }

        return $response;
    }

    public function task_create(Message $message, $params) {
        $model = TelegramAuth::find() -> where(['chat_id' => $message -> getFrom() -> getId()]) -> one();

        if (!$model) {
            return 'Для создания задач вы должны авторизоваться. Введите команду /login';
        }

        if (!isset($params[0]) || !isset($params[1]) || !isset($params[2]) || !isset($params[3]) || !isset($params[4])) {
            return 'Команда введена неверно';
        }

        $team = Teams::find() -> where(['name' => $params[1]]) -> one();

        if (!$team) {
            return 'Команды c таким именем не существует';
        }

        $user = User::findByUsername($params[2]);

        if (!$user) {
            return 'Пользователя c таким именем не существует';
        }

        $regexp = '/^\d{4}-\d{2}-\d{2}$/';

        if (!preg_match($regexp, $params[3])) {
            return 'Дата должна быть в формате Y-m-d';
        }

        $task = new Tasks([
            'name' =>  $params[0],
            'id_admin' => $model -> user_id,
            'id_user' => $user -> id,
            'deadline' => $params[3],
            'description' => $params[4],
            'id_team' => $team -> id
        ]);

        if ($task -> save()) {
            return 'Задача успешно создана';
        } else {
            return 'Ошибка при создании задачи';
        }
    }

    public function login(Message $message, $params) {
        if (!isset($params[0]) || !isset($params[1])) {
            return 'Команда введена неверно';
        } else {
            $user = User::findByUsername($params[0]);
            if (!$user) {
                return 'Пользователя с данным логином не существует';
            }

            if (!$user -> validatePassword($params[1])) {
                return 'Пароль не верен';
            }

            $model = new TelegramAuth([
                'user_id' => $user -> id,
                'chat_id' => $message -> getFrom() -> getId()
            ]);

            if ($model -> save()) {
                return 'Вы успешно авторизовались';
            } else {
                return 'Ошибка авторизации, обратитесь к администратору';
            }
        }
    }
}