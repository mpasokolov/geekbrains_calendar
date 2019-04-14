<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-11
 * Time: 19:24
 */

namespace common\models;


use SonkoDmitry\Yii\TelegramBot\Component;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Teams extends ActiveRecord {

    const EVENT_CREATE_TEAM = 'new_team';

    public function init() {
        parent::init();
        $this -> on(self::EVENT_CREATE_TEAM, [$this, 'sendTelegramNotify']);
    }

    public function behaviors() {
        return [
            'class' => TimestampBehavior::class,
        ];
    }

    public function attributeLabels() {
        return [
            'name' => 'Название команды',
            'teamlead' => 'Лидер команды',
            'parent_id' => 'Родительский проект'
        ];
    }

    public function rules() {
        return [
            ['name', 'string', 'max' => 50, 'message' => 'Поле не может быть длинее 50 символов'],
            [['teamlead', 'name'], 'required', 'message' => 'Поле обязательно для заполнения'],
            ['name', 'unique',
                'targetClass' => Teams::class,
                'targetAttribute' => 'name',
                'message' => 'Команда с таким именем уже существует'
            ]
        ];
    }

    public function sendTelegramNotify($event) {
        $users = TelegramSubscribe::find() -> asArray() -> all();
        foreach ($users as $user) {
            /** @var Component $bot */
            $bot = \Yii::$app -> bot;
            $bot -> sendMessage(
                $user['telegram_chat_id'],
                'Создана новая задача с именем: ' . $event -> sender -> getAttribute('name'));
        }

    }

    public function getTeams() {
        return $this -> hasMany(User::class, ['id' => 'id_user'])
            ->viaTable('users_teams', ['id_team' => 'id'], function($query) {
                $query -> where(['id_user' => \Yii::$app -> user -> id]);
            });
    }

    public function getUsers() {
        return $this -> hasMany(User::class, ['id' => 'id_user'])
            ->viaTable('users_teams', ['id_team' => 'id']);
    }

    public function getTeamLead() {
        return $this -> hasOne(User::class, ['id' => 'teamlead']);
    }

    public function getParentTeam() {
        return $this -> hasOne(Teams::class, ['id' => 'parent_id']);
    }

    public static function checkAccess($idTeam) {
        $team = self::findOne($idTeam);
        return !$team -> teamlead || $team -> teamlead !== \Yii::$app -> user -> id ? false : true;
    }

    public static function getTeamLeadId($teamId) {
        return self::findOne($teamId) -> teamlead;
    }

    public function getTasks() {
        return $this -> hasMany(Tasks::class, ['id_team' => 'id']);

    }

}