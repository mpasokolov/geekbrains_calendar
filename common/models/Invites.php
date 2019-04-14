<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-13
 * Time: 15:02
 */

namespace common\models;


use yii\db\ActiveRecord;

class Invites extends ActiveRecord {
    public function attributeLabels() {
        return [
            'id_to' => 'Отправить приглашение в команду'
        ];
    }

    public function rules() {
        return [
            [['id_to', 'id_team'], 'safe']
        ];
    }

    public function beforeSave($insert) {
        if (parent ::beforeSave($insert)) {
            $this -> id_from = \Yii::$app -> user -> id;
            return true;
        } else {
            return false;
        }
    }

    public function getReporterUser() {
        return $this -> hasOne(User::class, ['id' => 'id_from']);
    }

    public function getTeam() {
        return $this -> hasOne(Teams::class, ['id' => 'id_team']);
    }
}