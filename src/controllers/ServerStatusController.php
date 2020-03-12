<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 22/8/18
     * Time: 12:34 PM
     */

    namespace fatfish\notification\controllers;

    use craft\web\Controller;
    use fatfish\notification\records\NotificationServerRecord;
    use fatfish\notification\records\NotificationSettingRecord;
    use fatfish\notification\services\SendNotificationMessageService;
    use fatfish\notification\services\ServerNotificationService;
    use Craft;

    class ServerStatusController extends Controller
    {
        /**
         *
         */
        public static function check_server_status()
        {
            $Servers=NotificationServerRecord::find()->select('id,server_port')->all();
            $ip = gethostbyname('localhost');
            $NotificationSettings = NotificationSettingRecord::find()->select('slack,email')->all();
            if (!empty($Servers)) {
                foreach ($Servers as $server) {
                    $fp = fsockopen($ip, $server->server_port, $errorno, $errstr);
                    if (!$fp) {
                        ServerNotificationService::UpdateServerStatus($server->id, 0);
                        $data = ['text'=>'Service running on port '.$server->server_port.' is Offline'];
                        if (!empty($NotificationSettings[0]['slack']) && !is_null($NotificationSettings[0]['slack'])) {
                            SendNotificationMessageService::sendSlackMessage(json_encode($data), $NotificationSettings[0]['slack']);
                        } elseif (!empty($NotificationSettings[0]['email']) && !is_null($NotificationSettings[0]['email'])) {
                            SendNotificationMessageService::sendEmail($data, $NotificationSettings[0]['email']);
                        }
                    } else {
                        ServerNotificationService::UpdateServerStatus($server->id, 1);
                    }
                }
            }
        }
    }
