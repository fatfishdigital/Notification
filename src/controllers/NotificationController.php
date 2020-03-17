<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 6/8/18
     * Time: 3:23 PM
     */

    namespace fatfish\notification\controllers;

    use Craft;
    use craft\web\Controller;
    use fatfish\notification\models\CraftNotificationModel;
    use fatfish\notification\models\NotificationSettingsModel;
    use fatfish\notification\models\ServerNotificationModel;
    use fatfish\notification\records\NotificationRecord;
    use fatfish\notification\records\NotificationServerRecord;
    use fatfish\notification\records\NotificationSettingRecord;
    use fatfish\notification\services\CraftNotificationService;
    use fatfish\notification\services\NotificationSettingService;
    use fatfish\notification\services\ServerNotificationService;

    class NotificationController extends Controller
    {
        public $ServerList;
        public $NotificationData = [];
        public $sectionType=[
                0=>'Null',
                1=>'channel',
                2=>'structure',
                3=>'single'

        ];
        public $ElementType=[
                0=>'Null',
                1=>'Entries',
                2=>'Assets',
                3=>'User',
        ];


        public function actionIndex()
        {

            $NotificationService = new ServerNotificationService();
            $this->ServerList = $NotificationService->getAllServer();

            return $this->renderTemplate('notification/index', ['Servers' => $this->ServerList, 'server' => null]);
        }


        public function actionSave()
        {

            $ServerData = Craft::$app->request->post();
            $ServerNotificationModel = new ServerNotificationModel();
            $ServerNotificationModel = $ServerData;
             $NotificationService = new ServerNotificationService();
            if (!$NotificationService->SaveServer($ServerNotificationModel))
              {
                    Craft::$app->getSession()->setNotice('Unable to save data');
                    exit;
             }
            Craft::$app->getSession()->setNotice('Data Saved Successfully !');
            ServerStatusController::check_server_status();
            return $this->redirect('notification');
        }


        public function actionCraft()
        {
            $CraftNotification = NotificationRecord::find()->all();



            return $this->renderTemplate('notification/craftnotification/_craftnotification', ['notifications' => $CraftNotification]);
        }


        public function actionEdit($id)
        {
            $this->ServerList = ServerNotificationService::getServer($id);

            return $this->renderTemplate('notification/index', ['server' => $this->ServerList]);
        }


        public function actionDelete($id)
        {
            if (ServerNotificationService::DeleteServer($id))
            {
                Craft::$app->getSession()->setNotice('Item Deleted !!!');
            }
            else {
                Craft::$app->getSession()->setNotice('Cannot Delete item');
            }
            $this->redirect('notification');
            //return $this->renderTemplate('notification/index', ['Servers' => $this->ServerList, 'server' => null]);
        }


        /*
         * returns SectionType
         * @param sectionName
         */
        public function actionSection()
        {
            $AllSection = [];
            if (Craft::$app->request->isAjax) {
                $SectionType = Craft::$app->request->getBodyParam('sectionHandel');

                $AllSectionType = Craft::$app->getSections()->getSectionsByType($SectionType);

                foreach ($AllSectionType as $section) {
                    $AllSection[$section->id] = $section->name;
                }

                return json_encode($AllSection);
            }

        }
        public function getSectionName($handle)
        {
                $AllSection = [];
             $SectionType = $handle;
              $AllSectionType = Craft::$app->getSections()->getSectionsByType($SectionType);
               foreach ($AllSectionType as $section) {
                    $AllSection[$section->id] = $section->name;
                }
                return ($AllSection);


        }

        /*
         * Save Notification Type
         * returns bool
         */
        public function actionSavenotification()
        {

            $CraftNotificationModel = new CraftNotificationModel();
            $CraftNotificationModel->id = Craft::$app->request->getBodyParam('id');
            $CraftNotificationModel->Notification_name = Craft::$app->request->getBodyParam('element_name');
            $CraftNotificationModel->Notification_type = Craft::$app->request->getBodyParam('element_type');
            $CraftNotificationModel->Notification_section = Craft::$app->request->getBodyParam('section_type');
            $CraftNotificationModel->Notification_section_list = Craft::$app->request->getBodyParam('entries');
            $CraftNotificationModel->Notification_create = (int)Craft::$app->request->getBodyParam('Create');
            $CraftNotificationModel->Notification_update = (int)Craft::$app->request->getBodyParam('update');
            $CraftNotificationModel->Notification_delete = (int)Craft::$app->request->getBodyParam('Delete');
            $CraftNotificationModel->Notification_exception = (int)Craft::$app->request->getBodyParam('RequestResponse');
            $NotificationService = new CraftNotificationService();
            $NotificationService->SaveCraftNotification($CraftNotificationModel);
            $this->actionCraft();
        }

        /*
         * render settings page
         */
        public function actionSetting()
        {
            if (Craft::$app->request->isPost) {
                $NotificationSettingsModel = new NotificationSettingsModel();
                $NotificationSettingsModel->id = Craft::$app->request->getBodyParam('id');
                $NotificationSettingsModel->email = Craft::$app->request->getBodyParam('email');
                $NotificationSettingsModel->slack = Craft::$app->request->getBodyParam('Slack');
                $NotificationSettingsModel->craftemail = Craft::$app->request->getBodyParam('craftemail');
                $NotificationSettingsModel->craftslack = Craft::$app->request->getBodyParam('craftSlack');
                $NotificationService = new NotificationSettingService();
                $NotificationService->SaveNotificationSetting($NotificationSettingsModel);
            }
            $NotifcationSettingRecords = NotificationSettingRecord::find()->all();
            if (!is_null($NotifcationSettingRecords) and is_array($NotifcationSettingRecords)) {
                Craft::$app->session->set('setting', $NotifcationSettingRecords);
            }


            return $this->renderTemplate('notification/settings/settings', ['settings' => $NotifcationSettingRecords]);
        }


        /*
         *
         * Edit craft notification
         */
        public function actionCraftnotificationedit($id)
        {
            $Id= (int)$id;
            $NotificationSettingsRecord = NotificationRecord::findAll(['id'=>$Id]);
            $FieldType= ($this->sectionType[$NotificationSettingsRecord[0]->Notification_section]);
            return $this->renderTemplate('notification/craftnotification/_craftnotification',
                    ['allnotifications' => $NotificationSettingsRecord,'section_type'=> $this->getSectionName($FieldType)]);
        }

        /*
         *
         * Delete craft notification
         * @params $id
         */


        public function actionDeletecraftnotification($id)
        {

            if (isset($id) && !is_null($id)) {
                NotificationRecord::deleteAll(['id'=>$id]);
                return $this->redirect('notification/craft');
            }
        }
    }
