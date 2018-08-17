<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 17/8/18
     * Time: 10:42 AM
     */

    namespace fatfish\notification\services;
    use craft\base\Component;
    use fatfish\notification\models\CraftNotificationModel;
    use fatfish\notification\records\NotificationRecord;

    class CraftNotificationService extends Component
    {





        public function SaveCraftNotification(CraftNotificationModel $craftnotification)
        {

            $CraftNotificationRecord = New NotificationRecord();
           $CraftNotificationRecord->Notification_name=$craftnotification->Notification_name;
           $CraftNotificationRecord->Notification_type=$craftnotification->Notification_type;
           $CraftNotificationRecord->Notification_section=$craftnotification->Notification_section;
           $CraftNotificationRecord->Notification_section_list=$craftnotification->Notification_section_list;
           $CraftNotificationRecord->Notification_create=$craftnotification->Notification_create;
           $CraftNotificationRecord->Notification_update=$craftnotification->Notification_update;
           $CraftNotificationRecord->Notification_delete=$craftnotification->Notification_delete;
           $CraftNotificationRecord->Notification_edit=$craftnotification->Notification_edit;
            $CraftNotificationRecord->save(true);






        }

    }