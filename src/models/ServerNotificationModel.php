<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 6/8/18
     * Time: 4:03 PM
     */

    namespace fatfish\notification\models;

    use craft\base\Model;
    class ServerNotificationModel extends Model
    {
        public $server_name;
        public $server_port;
        public $server_threshold;

        public function rules()
        {
            return [

                ['server_name','required'],
                ['server_port','required'],
                ['server_threshold','required'],
                ['server_ip','required'],


            ];
        }


    }
