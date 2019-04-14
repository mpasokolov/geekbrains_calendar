<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-08
 * Time: 18:38
 */

namespace common\models;


use yii\db\ActiveRecord;

class Messages extends ActiveRecord {
    public function getData() {
        return \Yii::$app -> user ->id;
    }

    public function getUsers() {
        return $this -> hasOne(User::class, ['id' => 'id_user']);
    }
}
