<?php
    /**
     * Notification plugin for Craft CMS 3.x
     *
     * Notification plugin for craft 3.x
     *
     * @link      https://fatfish.com.au
     * @copyright Copyright (c) 2018 Fatfish
     */

    namespace fatfish\notification\migrations;
    use Craft;
    use craft\db\Migration;


    /**
     * Notification Install Migration
     *
     * If your plugin needs to create any custom database tables when it gets installed,
     * create a migrations/ folder within your plugin folder, and save an Install.php file
     * within it using the following template:
     *
     * If you need to perform any additional actions on install/uninstall, override the
     * safeUp() and safeDown() methods.
     *
     * @author    Fatfish
     * @package   Notification
     * @since     1.0.0
     */
    class Install extends Migration
    {
        // Public Properties
        // =========================================================================

        /**
         * @var string The database driver to use
         */
        public $driver;

        // Public Methods
        // =========================================================================

        /**
         * This method contains the logic to be executed when applying this migration.
         * This method differs from [[up()]] in that the DB logic implemented here will
         * be enclosed within a DB transaction.
         * Child classes may implement this method instead of [[up()]] if the DB logic
         * needs to be within a transaction.
         *
         * @return boolean return a false value to indicate the migration fails
         * and should not proceed further. All other return values mean the migration succeeds.
         */
        public function safeUp()
        {
            $this->driver = Craft::$app->getConfig()->getDb()->driver;
            if ($this->createTables()) {
            $this->CreateServerLogs();
            $this->CreateNotificationRecord();
                $this->CreateNotificationSettingTable();
            }
        $this->addForeignKeys();
            return true;
        }

        /**
         * This method contains the logic to be executed when removing this migration.
         * This method differs from [[down()]] in that the DB logic implemented here will
         * be enclosed within a DB transaction.
         * Child classes may implement this method instead of [[down()]] if the DB logic
         * needs to be within a transaction.
         *
         * @return boolean return a false value to indicate the migration fails
         * and should not proceed further. All other return values mean the migration succeeds.
         */
        public function safeDown()
        {
            $this->driver = Craft::$app->getConfig()->getDb()->driver;
            $this->removeTables();

            return true;
        }

        // Protected Methods
        // =========================================================================

        /**
         * Creates the tables needed for the Records used by the plugin
         *
         * @return bool
         */
        protected function createTables()
        {
        $tablesCreated = false;

    // notification_notificationrecord table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%notification_server_record}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%notification_server_record}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'server_name'=>$this->string(45),
                    'server_port'=>$this->string(45),
                    'server_threshold'=>$this->string(45)
                ]
            );
        }

        return $tablesCreated;

        }



        /**
         * Creates the foreign keys needed for the Records used by the plugin
         *
         * @return void
         */
        protected function addForeignKeys()
        {
            // notification_notificationrecord table
            $this->addForeignKey(
                'ServerlogForeignKey',
                '{{%notification_server_logs}}',
                'server_id',
                '{{%notification_server_record}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        /**
         * Populates the DB with the default data.
         *
         * @return void
         */
        protected function insertDefaultData()
        {
        }

        /**
         * Removes the tables needed for the Records used by the plugin
         *
         * @return void
         */
        protected function removeTables()
        {
            // notification_notificationrecord table
            $this->dropTableIfExists('{{%notification_server_logs}}');
            $this->dropTableIfExists('{{%notification_server_record}}');
            $this->dropTableIfExists('{{%NotificationSettings}}');
            $this->dropTableIfExists('{{%NotificationRecord}}');
        }


        protected function CreateServerLogs()
        {

        $this->createTable('{{%notification_server_logs}}',[

            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'server_id'=>$this->integer(),
            'server_status'=>$this->integer(),
            'server_last_check'=>$this->string(45)


        ]);
        }

        protected function CreateNotificationRecord()
        {

            $this->createTable('{{%NotificationRecord}}', [
                'id'                            => $this->primaryKey(),
                'Notification_name'             => $this->string(45),
                'Notification_type'             => $this->string(45)->notNull(),
                'Notification_section'          => $this->string(45)->null(),
                'Notification_section_list'     => $this->string(45)->null(),
                'Notification_create'           => $this->boolean(),
                'Notification_update'           => $this->boolean(),
                'Notification_delete'           => $this->boolean(),
                'Notification_edit'             => $this->boolean(),
                'uid'                           => $this->uid(),
                'dateCreated'                   => $this->dateTime()->notNull(),
                'dateUpdated'                   => $this->dateTime()->notNull(),
            ]);
            return true;
        }


        protected function CreateNotificationSettingTable()
        {
             $this->createTable('{{%NotificationSettings}}',[
                'id'             =>$this->primaryKey(),
                'email'          =>$this->string(500),
                'slack'          =>$this->string(500),
                'craftemail'     =>$this->string(500),
                'craftslack'     =>$this->string(500),
                'uid'            =>$this->uid(),
                'dateCreated'    =>$this->dateTime()->notNull(),
                'dateUpdated'    =>$this->dateTime()->notNull()
            ]);

        }
    }
