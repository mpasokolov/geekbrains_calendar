<?php

use yii\db\Migration;

/**
 * Class m190108_181743_teams
 */
class m190108_181743_teams extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('teams', [
            'id' => $this -> primaryKey(),
            'name' => $this -> string(45) -> notNull() -> unique(),
            'teamlead' => $this -> integer() -> notNull(),
            'status' => $this -> boolean() ->defaultValue(true),
            'parent_id' => $this -> integer() -> defaultValue(null),
            'created_at' => $this -> integer(10) -> notNull(),
            'updated_at' => $this -> integer(10)
        ]);

        $this -> addForeignKey('teams-users', 'teams', 'teamlead', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropForeignKey('teams-users', 'teams');
        $this -> dropTable('teams');
    }
}
