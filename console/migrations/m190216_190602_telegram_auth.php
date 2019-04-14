<?php

use yii\db\Migration;

/**
 * Class m190216_190602_telegram_auth
 */
class m190216_190602_telegram_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('telegram_auth', [
            'id' => $this -> primaryKey(),
            'user_id' => $this -> integer() -> notNull(),
            'chat_id' => $this -> integer() -> notNull(),
        ]);

        $this -> addForeignKey(
            'telegram_auth-user',
            'telegram_auth',
            'user_id',
            'users',
            'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropForeignKey('telegram_auth-user', 'telegram_auth');
        $this -> dropTable('telegram_auth');
    }
}