<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%prize}}`.
 */
class m220310_100415_create_prize_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%prize}}', [
            'id' => $this->primaryKey(),
			'user_id' => $this->integer(11),
			'type' => $this->string(),
			'value' => $this->string(),
			'refused' => $this->tinyInteger(1)->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%prize}}');
    }
}
