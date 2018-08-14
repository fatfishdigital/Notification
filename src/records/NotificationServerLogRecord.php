<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 8/8/18
     * Time: 11:53 AM
     */

    namespace fatfish\notification\records;
     use craft\db\ActiveRecord;
     use fatfish\notification\services\ServerNotificationService;
     use yii\db\ActiveQueryInterface;
    use yii\db\BaseActiveRecord;
    use yii\db\ActiveQuery;
    class NotificationServerLogRecord extends ActiveRecord
    {

        public static function tableName()
        {
            return '{{%notification_server_logs}}';
        }



    }