<?php

use yii\db\Migration;

/**
 * Class m190108_182836_tasks
 */
class m190108_182836_tasks extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('tasks', [
            'id' => $this -> primaryKey(),
            'name' => $this -> string(100) -> notNull(),
            'description' => $this -> text(),
            'id_admin' => $this -> integer() -> notNull(),
            'id_user' => $this -> integer(),
            'id_team' => $this -> integer() -> notNull(),
            'deadline' => $this -> integer(10),
            'finish' => $this -> boolean() -> defaultValue(false),
            'finish_time' => $this -> integer(10) -> defaultValue(null),
            'report' => $this -> text() -> defaultValue(null),
            'created_at' => $this -> integer(10) ->notNull(),
            'updated_at' => $this -> integer(10)
        ]);

        $this -> addForeignKey('tasks_users_admin', 'tasks', 'id_admin', 'users', 'id');
        $this -> addForeignKey('tasks_users_user', 'tasks', 'id_user', 'users', 'id');
        $this -> addForeignKey('tasks_teams', 'tasks', 'id_team', 'teams', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropForeignKey('tasks_users_admin', 'tasks');
        $this -> dropForeignKey('tasks_users_user', 'tasks');
        $this -> dropForeignKey('tasks_teams', 'tasks');

        $this -> dropTable('tasks');
    }
}
