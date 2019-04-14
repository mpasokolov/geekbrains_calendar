<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-28
 * Time: 19:32
 */

namespace common\components;


use common\models\Tasks;
use yii\base\Component;
use yii\base\Event;
use yii\caching\TagDependency;

class BootstrapComponents extends Component {
    public function init() {
        Event::on(Tasks::class, Tasks::EVENT_AFTER_DELETE, function (Event $event) {
            $this -> usersTaskSearch($event -> sender);
        });
        Event::on(Tasks::class, Tasks::EVENT_AFTER_INSERT, function (Event $event) {
            $this -> usersTaskSearch($event -> sender);
        });
        Event::on(Tasks::class, Tasks::EVENT_AFTER_UPDATE, function (Event $event) {
            $this -> usersTaskSearch($event -> sender);
        });
    }

    private function usersTaskSearch($model) {
        if (!empty($model -> oldAttributes)) {
            TagDependency::invalidate(\Yii::$app -> cache, 'user_tasks_search_' . $model -> oldAttributes['id_user']);

            if ($model -> oldAttributes['id_user'] !== $model -> id_user) {
                TagDependency::invalidate(\Yii::$app -> cache, 'user_tasks_search_' . $model -> id_user);
            }
        } else {
            TagDependency::invalidate(\Yii::$app -> cache, 'user_tasks_search_' . $model -> sender -> id_user);
        }
    }
}