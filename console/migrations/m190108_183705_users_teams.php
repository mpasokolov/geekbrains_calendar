<?php

use yii\db\Migration;

/**
 * Class m190108_183705_users_teams
 */
class m190108_183705_users_teams extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('users_teams', [
            'id' => $this -> primaryKey(),
            'id_user' => $this -> integer(10) -> notNull(),
            'id_team' => $this -> integer(10) -> notNull()
        ]);

        $this -> addForeignKey('users-teams_users', 'users_teams', 'id_user', 'users', 'id');
        $this -> addForeignKey('users-teams_teams', 'users_teams', 'id_team', 'teams', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropForeignKey('users-teams_users', 'users_teams');
        $this -> dropForeignKey('users-teams_teams', 'users_teams');

        $this -> dropTable('users_teams');
    }
}
