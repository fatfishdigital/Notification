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
    use fatfish\notification\models\ServerNotificationModel;
    use fatfish\notification\services\ServerNotificationService;


    class NotificationController extends Controller
    {
        public $ServerList;




        public function actionIndex()
         {
             $NotificationService = new ServerNotificationService();
             $this->ServerList = $NotificationService->getAllServer();

             return $this->renderTemplate('notification/index',['Servers'=>$this->ServerList,'server'=>null]);

         }


        public function actionSave()
        {
            $ServerData = Craft::$app->request->post();
            $ServerNotificationModel = new ServerNotificationModel();
            $ServerNotificationModel = $ServerData;
            $NotificationService = new ServerNotificationService();
            if (!$NotificationService->SaveServer($ServerNotificationModel)) {

                Craft::info('Unable to save data');
                exit;
            }
            Craft::info('Data Saved Successfully !');
            return $this->redirect('notification');
        }



        public function actionCraft()
        {
            return $this->renderTemplate('notification/craftnotification/_craftnotification');
        }



        public function actionEdit($id)
        {
           $this->ServerList= ServerNotificationService::getServer($id);

           return $this->renderTemplate('notification/index',['server'=>$this->ServerList]);
         }


         public function actionDelete($id)
         {

             if(ServerNotificationService::DeleteServer($id))
             {

                 Craft::info('Item Deleted !!!');

             }
             else
             {
                 Craft::error('Cannot Delete item');
             }
             return $this->renderTemplate('notification/index',['Servers'=>$this->ServerList,'server'=>null]);
         }
    }