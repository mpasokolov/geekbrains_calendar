<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-03-30
 * Time: 14:41
 */

namespace common\models;

use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class SearchTasksByUser extends ActiveRecord implements Strategy
{
    public static function tableName() {
        return 'tasks';
    }

    private $query;

    public function getTasks() {
        $query = Tasks::find()
            -> where(['id_user' => \Yii::$app -> user -> id])
            -> joinWith('users')
            -> joinWith('teams');

        $this -> query = $query;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
        return $dataProvider;
    }

    public function getSortedTasks(ActiveDataProvider $dataProvider, $params, $extraData = []) {

        $dataProvider -> sort -> attributes['id_team'] = [
            'asc'  => ['teams.name' => SORT_ASC],
            'desc' => ['teams.name' => SORT_DESC],
        ];

        \Yii::$app -> db -> cache(function () use($dataProvider) {
            return $dataProvider -> prepare();
        }, 60 * 60 * 24, new TagDependency(['tags' => 'user_tasks_search_' . \Yii::$app -> user -> id]));

        return $dataProvider;
    }
}