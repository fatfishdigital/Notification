<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 20/8/18
     * Time: 10:39 AM
     */

    namespace fatfish\notification\models;
    use craft\base\Model;

    class NotificationSettingsModel extends Model
    {

        public $email;
        public $slack;
        public $type;
        public $craftemail;
        public $craftslack;
        public $id;

        public function rules()
        {
            return [
                ['id','null'],
                ['email','null'],
                ['slack','null'],
                ['craftemail','null'],
                ['craftslack','null'],

            ];
        }


    }