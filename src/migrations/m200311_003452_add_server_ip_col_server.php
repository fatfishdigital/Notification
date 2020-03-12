<?php

namespace fatfish\notification\migrations;

use Craft;
use craft\db\Migration;

/**
 * m200311_003452_add_server_ip_col_server migration.
 */
class m200311_003452_add_server_ip_col_server extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('notification_server_record','server_ip','varchar(255)');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200311_003452_add_server_ip_col_server cannot be reverted.\n";
        return false;
    }
}
