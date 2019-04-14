<?php

use yii\db\Migration;

/**
 * Class m190209_140620_history
 */
class m190209_140620_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('history', [
            'id' => $this -> primaryKey(),
            'id_task' => $this -> integer() -> notNull(),
            'id_user' => $this -> integer() -> notNull(),
            'id_message' => $this -> integer() -> notNull(),
        ]);

        $this -> addForeignKey('history-user', 'history', 'id_user', 'users', 'id');
        $this -> addForeignKey('history-task', 'history', 'id_task', 'tasks', 'id');
        $this -> addForeignKey('history-message', 'history', 'id_message', 'messages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropForeignKey('history-user', 'history');
        $this -> dropForeignKey('history-task', 'history');
        $this -> dropForeignKey('history-message', 'history');

        $this -> dropTable('history');
    }
}
