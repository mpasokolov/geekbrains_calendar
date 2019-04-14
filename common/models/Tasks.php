<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

class Tasks extends ActiveRecord {
    const SCENARIO_CREATE = 'create';
    const SCENARIO_FINISH = 'finish';

    public function behaviors() {
        return [
            'class' => TimestampBehavior::class,
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'id_user', 'id_team', 'deadline', 'description', 'id_admin'];
        $scenarios[self::SCENARIO_FINISH] = ['finish', 'report'];
        return $scenarios;
    }

    public function attributeLabels() {
        return [
            'name' => 'Название задачи',
            'id_team' => 'Название команды',
            'id_user' => 'Имя исполнителя',
            'deadline' => 'Срок исполнения',
            'description' => 'Описание задачи',
            'report' => 'Отчет о выполнении'
        ];
    }

    public function rules() {
        return [
            ['name', 'string', 'max' => 100],
            ['deadline', 'date', 'format' => 'php:Y-m-d'],
            ['description', 'string'],
            [['name', 'id_user', 'deadline', 'description', 'id_team', 'id_admin'], 'required',
                'message' => 'Поле обязательно для заполнения'],
            ['deadline', 'compareDate', 'on' => self::SCENARIO_CREATE],
            ['report', 'required',
                'message' => 'Поле обязательно для заполнения', 'on' => self::SCENARIO_FINISH],
            ['finish', 'safe', 'on' => self::SCENARIO_FINISH],
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            if ($this -> report) {
                $this -> finish = true;
                $this -> finish_time = time();
            } else {
                $this -> deadline = strtotime($this -> deadline);
            }

            return true;
        } else {
            return false;
        }
    }

    public function getAdmins() {
        return $this -> hasOne(User::class, ['id' => 'id_admin']);
    }

    public function getUsers() {
        return $this -> hasOne(User::class, ['id' => 'id_user']);
    }

    public function getTeams() {
        return $this -> hasOne(Teams::class, ['id' => 'id_team']);
    }

    public function compareDate() {
        $date = getdate();
        if (strtotime($this -> deadline) < mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year'])) {
            $this -> addError('deadline', 'Дата завершения должна быть больше или равна текущей даты');
        }
    }

    public static function checkAccess($model) {
        $userId = \Yii::$app -> user -> id;
        if ($model -> id_user === $userId or $model -> id_admin === $userId) {
            return true;
        }

        return false;
    }
}