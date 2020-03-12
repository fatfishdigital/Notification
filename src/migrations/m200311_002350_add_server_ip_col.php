<?php

namespace fatfish\notification\migrations;

use Craft;
use craft\db\Migration;

/**
 * m200311_002350_add_server_ip_col migration.
 */
class m200311_002350_add_server_ip_col extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
       $this->addColumn('notification_server_logs','server_ip','varchar(255)');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        echo "m200311_002350_add_server_ip_col cannot be reverted.\n";
        return false;
    }
}
