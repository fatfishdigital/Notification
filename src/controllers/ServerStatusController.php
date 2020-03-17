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
            $SystemXml = CRAFT_BASE_PATH."/storage/notification/system.xml";
            if(!file_exists($SystemXml) || !file_exists($serverxml))
            {
                syslog(1,"Required file doesnot exist");
                exit;
            }
            $xml = simplexml_load_file($SystemXml);
            $SystemSlack = (array)$xml->Server->Slack;
            $SystemEmail = (array)$xml->Server->Email;
            $serverXml  = simplexml_load_file($serverxml);
            foreach ($serverXml as $server) {
                $ip=gethostbyaddr($server->server_ip);
                $fp = @fsockopen($ip, (int)$server->port, $err, $errstr);
                if (!$fp) {
                    $data = ['text'=>'Service running on port '.$server->port.' is Offline'];
                    ServerNotificationService::UpdateServerStatus((int)$server->id, 0);
                    if(sizeof($SystemSlack)<0) {
                        SendNotificationMessageService::sendSlackMessage(json_encode($data),$server->port,$SystemSlack[0]);
                    }
                    if(sizeof($SystemEmail)<0) {
                        SendNotificationMessageService::sendEmail($data,$SystemEmail[0]);
                    }

                }else
                {
                    ServerNotificationService::UpdateServerStatus((int)$server->id, 1);
                }
            }

        }
    }
