<?php
/**
 * Notification plugin for Craft CMS 3.x
 *
 * Notification plugin for craft 3.x
 *
 * @link      https://fatfish.com.au
 * @copyright Copyright (c) 2018 Fatfish
 */

namespace fatfish\notification\services;


use fatfish\notification\models\ServerNotificationModel;
use fatfish\notification\Notification;

use Craft;
use craft\base\Component;
use fatfish\notification\records\NotificationServerLogRecord;
use fatfish\notification\records\NotificationServerRecord;
use yii\db\Command;

/**
 * CraftNotificationService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Fatfish
 * @package   Notification
 * @since     1.0.0
 */
class ServerNotificationService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Notification::$plugin->notificationService->exampleService()
     *
     * @param ServerNotificationModel $serverModel
     * @return mixed
     */
    public function SaveServer($serverModel)
    {


        try {
            if (isset($serverModel['server_id']) && !is_null((int)$serverModel['server_id']) && !empty($serverModel['server_id'])) {

                $NotificationServerRecord = NotificationServerRecord::findOne((int)$serverModel['server_id']);
                $NotificationServerRecord->id = (int)$serverModel['server_id'];
                $NotificationServerRecord->server_name = $serverModel['server_name'];
                $NotificationServerRecord->server_port = $serverModel['server_port'];
                $NotificationServerRecord->server_threshold = $serverModel['server_threshold'];
                $NotificationServerRecord->save(true);

            } else {

                $NotificationServerRecord = new NotificationServerRecord();
                $NotificationServerRecord->server_name = $serverModel['server_name'];
                $NotificationServerRecord->server_port = $serverModel['server_port'];
                $NotificationServerRecord->server_threshold = $serverModel['server_threshold'];
                $Notification_Server_Logs = new NotificationServerLogRecord();
                $NotificationServerRecord->save(true);
                $Notification_Server_Logs->server_id = $NotificationServerRecord->getAttribute('id');
                $Notification_Server_Logs->save();
            }
        } catch (\Exception $ex) {

            Craft::info($ex->getMessage());
        }

        return true;
    }

    /**
     *
     */
    public function GetAllServer()
    {

        $allServer = [];
        $Servers = NotificationServerRecord::find()->with('allServers')->all();

        foreach ($Servers as $server):
            $allServer[] =
                [
                    'id' => $server->id,
                    'server_name' => $server->server_name,
                    'server_threshold' => $server->server_threshold,
                    'server_port' => $server->server_port,
                    'server_status' => $server->allServers[0]['server_status'],
                    'server_last_check' => $server->allServers[0]['server_last_check'],


                ];

        endforeach;
        return $allServer;
    }

    public static function getServer($id)
    {

        $server_data = NotificationServerRecord::find()->where(['id' => $id])->one();
        return $server_data;

    }

    public static function DeleteServer($id)
    {
        $server = NotificationServerRecord::findOne($id);
        $server->delete();
        return true;
    }

    public static function UpdateServerStatus($id,$status)
    {

       $Serverlogs= NotificationServerLogRecord::findOne(['server_id'=>$id]);

       $Serverlogs->server_id = $id;
       $Serverlogs->server_status = $status;

       $Serverlogs->save();

          }

}