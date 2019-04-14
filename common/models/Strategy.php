<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-03-30
 * Time: 14:39
 */

namespace common\models;

use yii\data\ActiveDataProvider;

interface Strategy
{
    public function getSortedTasks(ActiveDataProvider $dataProvider, $params, $extraData = []);

    public function getTasks();
}