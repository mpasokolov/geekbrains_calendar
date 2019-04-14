<?php

use yii\db\Migration;

/**
 * Class m190216_172623_telegram_subscribe
 */
class m190216_172623_telegram_subscribe extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('telegram-subscribe', [
            'id' => $this -> primaryKey(),
            'channel' => $this -> string(),
            'telegram_chat_id' => $this -> integer() -> unique(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this -> dropTable('telegram-subscribe');
    }
}
