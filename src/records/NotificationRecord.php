<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 17/8/18
     * Time: 10:41 AM
     */

    namespace fatfish\notification\records;
    use craft\db\ActiveRecord;

    class NotificationRecord extends ActiveRecord
    {
        public static function tableName()
        {
            return '{{%NotificationRecord}}';
        }

    }