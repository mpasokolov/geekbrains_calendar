<?php

use yii\db\Migration;

/**
 * Class m190208_153117_messages
 */
class m190208_153117_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('messages', [
            'id' => $this -> primaryKey(),
            'message' => $this -> text() -> notNull(),
            'id_user' => $this -> integer() -> notNull(),
            'id_task' => $this -> integer() -> notNull(),
            'time' => $this -> integer(11) -> notNull(),
        ]);

        $this -> addForeignKey('messages-users', 'messages', 'id_user', 'users', 'id');
        $this -> addForeignKey('messages-tasks', 'messages', 'id_task', 'tasks', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropForeignKey('messages-tasks', 'messages');
        $this -> dropForeignKey('messages-users', 'messages');
        $this -> dropTable('messages');
    }


}
