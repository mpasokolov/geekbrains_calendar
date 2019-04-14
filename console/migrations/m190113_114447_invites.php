<?php

use yii\db\Migration;

/**
 * Class m190113_114447_invites
 */
class m190113_114447_invites extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('invites', [
            'id' => $this->primaryKey(),
            'id_from' => $this -> integer() -> notNull(),
            'id_to' => $this -> integer() -> notNull(),
            'id_team' => $this -> integer() -> notNull()
        ]);

        $this -> addForeignKey('invites-user-from', 'invites', 'id_from', 'users', 'id');
        $this -> addForeignKey('invites-user-to', 'invites', 'id_to', 'users', 'id');
        $this -> addForeignKey('invites-teams', 'invites', 'id_team', 'teams', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
       $this -> dropForeignKey('invites-user-from', 'invites');
       $this -> dropForeignKey('invites-user-to', 'invites');
       $this -> dropForeignKey('invites-teams', 'invites');

       $this -> dropTable('invites');
    }
}
