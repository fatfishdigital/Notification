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
    use fatfish\notification\services\ServerNotificationService;
    use Craft;
    use craft\base\Component;

    class ServerStatusController extends Controller
    {



        public static function check_server_status()
        {

                    $Servers=NotificationServerRecord::find()->select('id,server_port')->all();
                    $ip = gethostbyname('localhost');


                if(!empty($Servers))
                {

                    foreach($Servers as $server)
                    {


                        $fp = fsockopen($ip,$server->server_port,$errorno,$errstr);
                      if(!$fp)
                      {
                            ServerNotificationService::UpdateServerStatus($server->id,0);
                      }
                      else
                      {
                          ServerNotificationService::UpdateServerStatus($server->id,1);
                      }
                    }
                }
        }
    }