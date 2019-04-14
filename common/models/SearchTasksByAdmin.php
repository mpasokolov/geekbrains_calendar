<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-03-30
 * Time: 14:45
 */

namespace common\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class SearchTasksByAdmin extends ActiveRecord implements Strategy
{
    public static function tableName() {
        return 'tasks';
    }

    private $query;

    public function getSortedTasks(ActiveDataProvider $dataProvider, $params, $extraData = []) {
        $query = $this -> query;
        $query -> andFilterWhere(['tasks.id' => $this -> id]);

        $query -> andFilterWhere(['like', 'tasks.name', $this  -> name])
            -> andFilterWhere(['like', 'description', $this -> description])
            -> andFilterWhere(['like', 'a.username', $this -> id_admin])
            -> andFilterWhere(['like', 'u.username', $this -> id_user])
            -> andFilterWhere(['like', 't.name', $this -> id_team])
            -> andFilterWhere(['like', 'finish', $this -> finish]);

        if ($this -> deadline) {
            $filter = $this -> getDateFilterPeriod($params -> deadline);

            $query -> andFilterWhere(['between', 'deadline', $filter['startDay'], $filter['finishDay']]);
        }

        if ($this -> finish_time) {
            $filter = $this -> getDateFilterPeriod($params -> finish_time);
            $query -> andFilterWhere(['between', 'finish_time', $filter['startDay'], $filter['finishDay']]);
        }

        if ($extraData['extra'] === '2') {
            $date = getdate();
            $query -> andFilterWhere(['<', 'deadline', mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']) + 1])
                -> andFilterWhere(['=', 'finish', 0]);
        }


        if ($extraData['extra'] === '1') {
            $date = getdate();
            $nowTime = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
            $sevenDaysAgoTime = $nowTime - 86400 * 6;
            $query -> andFilterWhere(['=', 'finish', '1'])
                -> andFilterWhere(['>=', 'finish_time', $sevenDaysAgoTime]);
        }

        return $dataProvider;
    }

    public function getTasks() {
        $query = Tasks::find()
            -> joinWith('teams t')
            -> joinWith('users u')
            -> joinWith('admins a');

        $this -> query = $query;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],

        ]);

        return $dataProvider;
    }

    private function getDateFilterPeriod($date) {
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