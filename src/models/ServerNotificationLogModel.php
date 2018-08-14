<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 8/8/18
     * Time: 11:48 AM
     */

    namespace fatfish\notification\models;
    use craft\base\Model;

    class ServerNotificationLogModel extends Model
    {

        public $server_id;
        public $server_status;
        public $server_last_check;


        public function rules()
        {
            return [
                ['server_id','required'],
                ['server_status','default','0'],
                ['server_status','default','0']
            ];


        }

    }