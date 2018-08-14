<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 6/8/18
     * Time: 3:59 PM
     */

    namespace fatfish\notification\records;
    use fatfish\notification\models\ServerNotificationLogModel;
    use yii\db\ActiveRecord;
    use Craft;
    use yii\db\ActiveQueryInterface;
    use craft\db\Query;

    class NotificationServerRecord extends ActiveRecord
    {
            public static function tableName()
            {
                return'{{%notification_server_record}}';
            }
            public function getallServers()
            {

              return $this->hasMany(NotificationServerLogRecord::class,['server_id'=>'id']);

    }

    }