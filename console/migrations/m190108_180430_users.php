<?php

use yii\db\Migration;

/**
 * Class m190108_180430_users
 */
class m190108_180430_users extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('users', [
            'id' => $this -> primaryKey(),
            'username' => $this -> string(45) -> notNull() -> unique(),
            'email' => $this -> string(100) -> notNull() -> unique(),
            'password' => $this -> string(255) -> notNull(),
            'created_at' => $this -> integer(10) -> notNull(),
            'updated_at' => $this -> integer(10),
            'access_token' => $this->string(),
        ]);

        $this->insert('users', [
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@admin.ru',
            'password' => Yii::$app -> security -> generatePasswordHash('admin'),
            'created_at' => 1555353427,
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropTable('users');
    }
}
