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

use Craft;
use craft\base\Component;
use fatfish\notification\models\ServerNotificationModel;
use fatfish\notification\records\NotificationServerLogRecord;
use fatfish\notification\records\NotificationServerRecord;

/**
 * CraftNotificationService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author  Fatfish
 * @package Notification
 * @since   1.0.0
 */
class ServerNotificationService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @param  $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getServer($id)
    {
        $server_data = NotificationServerRecord::find()->where(['id' => $id])->one();
        return $server_data;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function DeleteServer($id)
    {
        $server = NotificationServerRecord::findOne($id);
        $server->delete();
        return true;
    }

    /**
     * @param $id
     * @param $status
     *
     * update server log table
     */
    public static function UpdateServerStatus($id, $status)
    {
        $Serverlogs = NotificationServerLogRecord::findOne(['server_id' => $id]);

        $Serverlogs->server_id = $id;
        $Serverlogs->server_status = $status;

        $Serverlogs->save();
    }

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
            if (isset($serverModel['server_id'])&& !is_null((int)$serverModel['server_id'])
                    && !empty($serverModel['server_id'])
            ) {
                $NotificationServerRecord = NotificationServerRecord::findOne((int)$serverModel['server_id']);
                $NotificationServerRecord->id = (int)$serverModel['server_id'];
                $NotificationServerRecord->server_name = $serverModel['server_name'];
                $NotificationServerRecord->server_port = $serverModel['server_port'];
                $NotificationServerRecord->server_threshold = $serverModel['server_threshold'];
                $NotificationServerRecord->server_ip = $serverModel['server_ip'];
                $NotificationServerRecord->save(true);
                $this->write_servers_to_xml();
            } else {
                $NotificationServerRecord = new NotificationServerRecord();
                $NotificationServerRecord->server_name = $serverModel['server_name'];
                $NotificationServerRecord->server_port = $serverModel['server_port'];
                $NotificationServerRecord->server_threshold = $serverModel['server_threshold'];
                $NotificationServerRecord->server_ip = $serverModel['server_ip'];
                $Notification_Server_Logs = new NotificationServerLogRecord();
                $NotificationServerRecord->save(true);
                $Notification_Server_Logs->server_id = $NotificationServerRecord->getAttribute('id');
                $Notification_Server_Logs->save();
                $this->write_servers_to_xml();
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());die;
            Craft::info($ex->getMessage());
        }

        return true;
    }

    public function write_servers_to_xml()
    {
        $AllServer = NotificationServerRecord::find()->all();

        $xml = new \DOMDocument();
        $xml->formatOutput = true;
        $xml_settings = $xml->createElement("Servers");
        foreach ($AllServer as $server) {
            $xml_server = $xml->createElement("Server");
            $xml_server->setAttribute('id', $server->id);
            $server_id = $xml->createElement("id", $server->id);
            $server_name = $xml->createElement("name", $server->server_name);
            $server_ip = $xml->createElement("server_ip", $server->server_ip);
            $server_port = $xml->createElement("port", $server->server_port);
            $server_threshold = $xml->createElement("threshold", $server->server_threshold);
            $xml_settings->appendChild($xml_server);
            $xml_server->appendChild($server_id);
            $xml_server->appendChild($server_name);
            $xml_server->appendChild($server_port);
            $xml_server->appendChild($server_threshold);
            $xml_server->appendChild($server_ip);


        }

        $xml->appendChild($xml_settings);
        $getcurrent = dirname(dirname(dirname(__FILE__)));
        $storescript = $getcurrent."/src/cron/server.xml";

        if (!file_exists($storescript)) {
            $fp=fopen($storescript,"w+");
            $xml->save($storescript);
            fclose($fp);


        }else
        {
            $xml->save($storescript);
        }

    }

    /**
     * @return array
     */
    public function GetAllServer()
    {
        $allServer = [];
        $Servers = NotificationServerRecord::find()->with('allServers')->all();
    foreach ($Servers as $server):

            $allServer[] =
                    [
                            'id'                => $server->id,
                            'server_name'       => $server->server_name,
                            'server_ip'          => $server->server_ip,
                            'server_threshold'  => $server->server_threshold,
                            'server_port'       => $server->server_port,
                            'server_status'     => $server->allServers[0]['server_status'],
                            'server_last_check' => $server->allServers[0]['server_last_check'],

                    ];

        endforeach;
        return $allServer;
    }
}
