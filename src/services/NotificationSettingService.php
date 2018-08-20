<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 20/8/18
     * Time: 12:37 PM
     */

    namespace fatfish\notification\services;
    use craft\base\Component;
    use fatfish\notification\models\NotificationSettingsModel;
    use fatfish\notification\records\NotificationSettingRecord;

    class NotificationSettingService extends Component
    {


        public function SaveNotificationSetting(NotificationSettingsModel $model)
        {


      if(isset($model->id) && !is_null($model->id)) {

          $NotificationSettingRecord = NotificationSettingRecord::findOne(['id'=>$model->id]);
          $NotificationSettingRecord->id = $model->id;
          $NotificationSettingRecord->email = $model->email;
          $NotificationSettingRecord->slack = $model->slack;
          $NotificationSettingRecord->craftemail = $model->craftemail;
          $NotificationSettingRecord->craftslack = $model->craftslack;
          $NotificationSettingRecord->save(true);
          return true;
      }
            $NotificationSettingRecord = new NotificationSettingRecord();
            $NotificationSettingRecord->email = $model->email;
            $NotificationSettingRecord->slack = $model->slack;
            $NotificationSettingRecord->craftemail = $model->craftemail;
            $NotificationSettingRecord->craftslack = $model->craftslack;
            $NotificationSettingRecord->save(true);
        return true;


        }



    }