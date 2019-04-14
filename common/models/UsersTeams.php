<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-11
 * Time: 22:03
 */

namespace common\models;


use yii\db\ActiveRecord;

class UsersTeams extends ActiveRecord {

    public function rules() {
        return [
            [['id_user', 'id_team'], 'safe']
        ];
    }

    public function getTeams() {
        return $this -> hasOne(Teams::class, ['id' => 'id_team']) ;
    }

    public function getUsers() {
        return $this -> hasOne(User::class, ['id' => 'id_user']) ;
    }
}