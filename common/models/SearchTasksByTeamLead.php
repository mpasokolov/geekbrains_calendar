<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-03-30
 * Time: 14:43
 */

namespace common\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class SearchTasksByTeamLead extends ActiveRecord implements Strategy
{
    public static function tableName() {
        return 'tasks';
    }

    public function getSortedTasks(ActiveDataProvider $dataProvider, $params, $extraData = []) {

        $dataProvider -> sort -> attributes['id_team'] = [
            'asc'  => ['teams.name' => SORT_ASC],
            'desc' => ['teams.name' => SORT_DESC],
        ];

        return $dataProvider;
    }

    public function getTasks() {
        $query = Tasks::find()
            -> joinWith('teams')
            -> joinWith('users')
            -> where(['teams.teamlead' => \Yii::$app -> user -> id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);

        return $dataProvider;
    }
}