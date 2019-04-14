<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-01-11
 * Time: 20:18
 */

namespace common\models;


use yii\data\ActiveDataProvider;

class TeamsSearch extends Teams {
    public $username;

    public static function tableName() {
        return 'teams';
    }

    public function rules() {
        return [
            [['teamlead', 'name', 'id', 'username', 'teams.name', 'status', 'parent_id'], 'safe'],
            [['created_at', 'updated_at'], 'match',
                'pattern' => '/^\d{4}(-\d{2})?(-\d{2})?$/',
                'message' => 'Дата должна быть в формате Y-m-d'
            ],
        ];
    }

    public function searchAllTeams($params) {
        $query = Teams::find()
            -> joinWith('teamLead t')
            -> joinWith('parentTeam tm');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this -> load($params);

        $query -> andFilterWhere(['like', 'id', $this -> id])
            -> andFilterWhere(['like', 'name', $this -> name])
            -> andFilterWhere(['like', 't.username', $this -> teamlead])
            -> andFilterWhere(['like', 'created_at', $this -> created_at])
            -> andFilterWhere(['like', 'created_at', $this -> updated_at])
            -> andFilterWhere(['like', 'status', $this -> status])
            -> andFilterWhere(['like', 'tm.name', $this -> parent_id]);

        if ($this -> created_at) {
            $filter = $this -> getDateFilterPeriod($this -> created_at);

            $query -> andFilterWhere(['between', 'created_at', $filter['startDay'], $filter['finishDay']]);
        }

        if ($this -> updated_at) {
            $filter = $this -> getDateFilterPeriod($this -> updated_at);
            $query -> andFilterWhere(['between', 'updated_at', $filter['startDay'], $filter['finishDay']]);
        }

        return $dataProvider;
    }

    public function searchTeamsByUser($params) {
        $query = UsersTeams::find() -> where(['id_user' => \Yii::$app -> user -> id])
            -> joinWith('teams');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this -> load($params);

        return $dataProvider;
    }

    public function searchUsersByTeam($params, $id) {
        $query = UsersTeams::find() -> where(['id_team' => $id]) -> joinWith('users');
        //$query = Teams::findOne($id) -> getUsers();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this -> load($params);

        return $dataProvider;
    }

    public function getDateFilterPeriod($date) {
        $dateArr = explode('-', $date);
        $seconds = 86400;
        $filter = [];

        if (!array_key_exists(1, $dateArr)) {
            $days =  date('z', mktime(0, 0, 0, 12, 31, $dateArr[0]));
            $seconds = ($days + 1) * 86400;
        }
        if (array_key_exists(1, $dateArr) && !array_key_exists(2, $dateArr)) {
            $days = cal_days_in_month(CAL_GREGORIAN, $dateArr[1], $dateArr[0]);
            $seconds = $days * 86400;
        }

        $filter['startDay'] = mktime(0, 0, 0, $dateArr[1] ?? 1, $dateArr[2] ?? 1, $dateArr[0]);
        $filter['finishDay'] = $filter['startDay'] + $seconds - 1;

        return $filter;
    }
}