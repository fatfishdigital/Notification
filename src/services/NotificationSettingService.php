<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 20/8/18
     * Time: 12:37 PM
     */

    namespace fatfish\notification\services;
    use craft\base\Component;
    use craft\helpers\App;
    use fatfish\notification\models\NotificationSettingsModel;
    use fatfish\notification\records\NotificationSettingRecord;
    use Craft;

    class NotificationSettingService extends Component
    {


        public function SaveNotificationSetting(NotificationSettingsModel $model)
        {


      if(!empty($model->id) && !is_null($model->id)) {

          $NotificationSettingRecord = NotificationSettingRecord::findOne(['id'=>$model->id]);
          $NotificationSettingRecord->id = $model->id;
          $NotificationSettingRecord->email = $model->email;
          $NotificationSettingRecord->slack = $model->slack;
          $NotificationSettingRecord->craftemail = $model->craftemail;
          $NotificationSettingRecord->craftslack = $model->craftslack;
          $this->write_system_config_xml($NotificationSettingRecord);
          return true;
      }
            $NotificationSettingRecord = new NotificationSettingRecord();
            $NotificationSettingRecord->email = $model->email;
            $NotificationSettingRecord->slack = $model->slack;
            $NotificationSettingRecord->craftemail = $model->craftemail;
            $NotificationSettingRecord->craftslack = $model->craftslack;
            $NotificationSettingRecord->save(true);
            $this->write_system_config_xml($NotificationSettingRecord);
        return true;


        }

        /**
         * @param $NotificationSettingRecord
         */
        public function write_system_config_xml($NotificationSettingRecord): void
        {
             $xml = new \DOMDocument();
            $xml_settings = $xml->createElement("Settings");
            $xml_server = $xml->createElement("Server");
            $xml_craft = $xml->createElement("Craft");
            $xml_serverslack = $xml->createElement("Slack");
            $xml_serveremail = $xml->createElement("Email");
            $xml_craft_email = $xml->createElement("Email");
            $xml_craft_slack = $xml->createElement("Slack");
            $xml_craft->appendChild($xml_craft_email);
            $xml_craft->appendChild($xml_craft_slack);
            $xml_craft_email->nodeValue = $NotificationSettingRecord->craftemail;
            $xml_craft_slack->nodeValue = $NotificationSettingRecord->craftslack;

            $xml_serverslack->nodeValue = $NotificationSettingRecord->slack;
            $xml_serveremail->nodeValue = $NotificationSettingRecord->email;
            $xml_settings->appendChild($xml_server);
            $xml_settings->appendChild($xml_craft);

            $xml_server->appendChild($xml_serverslack);
            $xml_server->appendChild($xml_serveremail);

            $xml->appendChild($xml_settings);
            $getcurrent = dirname(dirname( dirname(__FILE__)));
            $storescript = $getcurrent."/src/cron/system.xml";
             if(!file_exists($storescript))
            {
                $fp=fopen($storescript,"w+");
                if(!$fp)
                {
                    \Craft::$app->getSession()->setNotice("system.xml doesnot exist under cron folder");
                    return;
                }
                $xml->save($storescript);
                fclose($fp);
                $NotificationSettingRecord->save(true);

            }else {


                 try {
                     $xml->save($storescript);
                     $NotificationSettingRecord->save(true);

                 } catch(\Exception $e) {


                     Craft::$app->getSession()->setNotice($e->getMessage());
                 }
             }
        }


    }
