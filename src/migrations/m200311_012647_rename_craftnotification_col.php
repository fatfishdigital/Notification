<?php

namespace fatfish\notification\migrations;

use Craft;
use craft\db\Migration;

/**
 * m200311_012647_rename_craftnotification_col migration.
 */
class m200311_012647_rename_craftnotification_col extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameColumn('{{NotificationRecord}}','Notification_edit','Notification_exception');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200311_012647_rename_craftnotification_col cannot be reverted.\n";
        return false;
    }
}
