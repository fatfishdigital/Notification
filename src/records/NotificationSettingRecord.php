<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 20/8/18
     * Time: 12:28 PM
     */

    namespace fatfish\notification\records;
    use craft\db\ActiveRecord;

    class NotificationSettingRecord extends ActiveRecord
    {
        public static function tableName()
        {
            return '{{%NotificationSettings}}';
        }
    }