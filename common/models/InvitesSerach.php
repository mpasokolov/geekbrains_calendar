<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-13
 * Time: 18:30
 */

namespace common\models;


use yii\data\ActiveDataProvider;

class InvitesSerach extends Invites {
    public static function tableName() {
        return 'invites';
    }

    public function search($params) {
        $query = Invites::find() -> where(['id_to' => \Yii::$app -> user -> id])
            -> joinWith('reporterUser')
            -> joinWith('team');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this -> load($params);

        return $dataProvider;
    }
}