<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 17/8/18
     * Time: 10:43 AM
     */

    namespace fatfish\notification\models;
    use craft\base\Model;

    class CraftNotificationModel extends Model
    {
        public $Notification_name;
        public $Notification_type;
        public $Notification_section;
        public $Notification_section_list;
        public $Notification_create;
        public $Notification_update;
        public $Notification_delete;
        public $Notification_edit;
        public $Notification_RequestResponse;
        public function rules()
        {


            return [
                        ['Notification_name','required'],
                        ['Notification_type','required'],
                        ['Notification_section','null'],
                        ['Notification_section_list','null'],
                        ['Notification_create','required'],
                        ['Notification_update','required'],
                        ['Notification_delete','required'],
                        ['Notification_edit','required'],
//                        ['Notification_create','required'],

                   ];




        }

    }