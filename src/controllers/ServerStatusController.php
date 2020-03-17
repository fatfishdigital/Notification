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
            $serverxml = CRAFT_BASE_PATH."/storage/notification/server.xml";
            $SystemXml = CRAFT_BASE_PATH."/storage/notification/server.xml";
            if(!file_exists($SystemXml) || !file_exists($serverxml))
            {
                syslog(1,"Required file doesnot exist");
                exit;
            }

            $xml = simplexml_load_file($SystemXml);
            $SystemSlack = $xml->Server->Slack;
            $SystemEmail = $xml->Server->Email;
            $serverXml  = simplexml_load_file($serverxml);

            foreach ($serverXml as $server) {
                $ip=gethostbyname($server->server_ip);
                $fp = @fsockopen($ip, (int)$server->port, $err, $errstr);
                if (!$fp) {
                    $data = ['text'=>'Service running on port '.$server->server_port.' is Offline'];
                    ServerNotificationService::UpdateServerStatus($server->id, 0);
                    SendNotificationMessageService::sendSlackMessage(json_encode($data), $server->port, $SystemSlack);
                    SendNotificationMessageService::sendEmail($data, $SystemEmail);

                }else
                {
                    ServerNotificationService::UpdateServerStatus($server->id, 1);
                }
            }

        }
    }
