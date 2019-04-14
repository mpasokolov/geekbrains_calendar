<?php

namespace common\models;

class TasksSearch extends Tasks {

    public $extra;
    private $strategy;

    public static function tableName() {
        return 'tasks';
    }

    public function attributeLabels() {
        return [
            'extra' => 'Дополнительные параметры поиска:'
        ];
    }

    public function rules() {
        return [
            [['name', 'description', 'id_admin', 'id_user', 'finish', 'id_team', 'finish_time', 'created_at'], 'safe'],
            [['deadline'], 'match',
                'pattern' => '/^\d{4}(-\d{2})?(-\d{2})?$/',
                'message' => 'Дата должна быть в формате Y-m-d'
            ],
            [['extra', 'id'], 'safe']
        ];
    }

    public function setStrategy(Strategy $strategy) {
        $this -> strategy = $strategy;
    }

    public function searchTasks($params) {
        $dataProvider = $this -> strategy -> getTasks();

        $this -> load($params);

        if (!$this -> validate()) {
            return $dataProvider;
        }

        $dataProvider = $this -> strategy -> getSortedTasks($dataProvider, $params, ['extra' => $this -> extra]);

        return $dataProvider;
    }
}