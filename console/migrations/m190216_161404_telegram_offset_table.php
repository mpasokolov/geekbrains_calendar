<?php

use yii\db\Migration;

/**
 * Class m190216_161404_telegram_offset_table
 */
class m190216_161404_telegram_offset_table extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this -> createTable('telegram_offset', [
            'id' => $this -> integer(),
            'timestamp_offset' => $this -> timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
       $this -> dropTable('telegram_offset');
    }
}
