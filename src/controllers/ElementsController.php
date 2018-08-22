<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 13/8/18
     * Time: 12:11 PM
     */

    namespace fatfish\notification\controllers;

    use craft\web\Controller;
    use Craft;
    use fatfish\notification\records\NotificationRecord;
    use fatfish\notification\services\SendNotificationMessageService;

    class ElementsController extends Controller
    {
        public $UserName;
        public $ElementType;
        public $ActionType;
        public $ElementTitle;
        public $updatedDate;
        public $createdDate;
        public $IselementNew;
        public $title;
        public $fileSize;
        public $fileType;
        public $firstname;
        public $lastname;
        public $email;
        public $CraftSlackUrl;
        public $CraftEmail;
        public $SystemSlack;
        public $SystemEmail;
        public $AllElementType;
        public $ElementTypeId;




        public function __construct()
        {



            $this->AllElementType = NotificationRecord::find()->select('Notification_type')->all();
            $NotificationSetting = Craft::$app->session->get('setting');
            $this->CraftSlackUrl = $NotificationSetting[0]['craftslack'];
            $this->CraftEmail    = $NotificationSetting[0]['craftemail'];

             }

        /**
         * @param $event
         */
        public function actionOnSaveElementEvent($event)
        {

            $this->ParseElement($event);




        if($this->SearchRequest($this->AllElementType)) {




            $data = '';
            $ElementType = str_replace('craft\elements\\', '',
                Craft::$app->getElements()->getElementTypeById($event->element->id));



            if ($this->createdDate['date'] === $this->updatedDate['date']) {
                $data = [
                    'text' => $ElementType . ' has been Created By ' . $this->UserName . ' [ Title: ' . $this->title . ' Created Date: ' . $this->createdDate['date'] . ']'
                ];
            } else {
                $data = [
                    'text' => $ElementType . ' has been updated By ' . $this->UserName . ' [ `Title: ` `' . $this->title . '` Updated Date: ' . $this->createdDate['date'] . ']'
                ];
            }

            SendNotificationMessageService::sendSlackMessage(json_encode($data), $this->CraftSlackUrl);
            SendNotificationMessageService::sendEmail($data,$this->CraftEmail);

        }

        }


        /**
         * @param $event
         */
        public function actionOnDeleteElements($event)
        {

            $this->ParseElement($event);

            if ($this->SearchRequest($this->AllElementType)) {
                $data = [
                    'text' => $this->ElementType . ' has been delete By ' . $this->UserName . ' [ `Title: ` `' . $this->title . '` Updated Date: ' . $this->createdDate['date'] . ']'
                ];

                SendNotificationMessageService::sendSlackMessage(json_encode($data), $this->CraftSlackUrl);
                SendNotificationMessageService::sendEmail($data,$this->CraftEmail);

            }
        }

        /**
         * @param $code
         * @param $message
         */
        public function actionOnResponse($code,$message)
        {

                $data = [
                    'text'=>'` '.$code.' '.$message.'` '
                ];
            SendNotificationMessageService::sendSlackMessage(json_encode($data),$this->CraftSlackUrl);
            SendNotificationMessageService::sendEmail($data,$this->CraftEmail);

        }

        /*
         *
         */
        /**
         * @param $event
         *
         */
        public function ParseElement($event)
        {



            switch ($event->element)
            {
                case $event->element instanceof \craft\elements\Entry:
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->createdDate = (array)$event->element->dateCreated;
                    $this->updatedDate = (array)$event->element->dateUpdated;
                    $this->title=        $event->element->title;
                    $this->ElementType = 'Entry';
                    $this->ElementTypeId='1';
                    break;

                case $event->element instanceof \craft\elements\Asset:
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->title = $event->element->title;
                    $this->fileSize = $event->element->size;
                    $this->fileType = $event->element->kind;
                    $this->ElementType = 'Asset';
                    $this->ElementTypeId='2';
                    break;

                case $event->element instanceof \craft\elements\User:

                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->title = $event->element->username;
                    $this->firstname = $event->element->firstname;
                    $this->lastname = $event->element->lastname;
                    $this->email    = $event->element->email;
                    $this->ElementType = 'User';
                    $this->ElementTypeId='3';
                    break;

                case $event->element instanceof \craft\elements\Category:
                    $this->title = $event->element->title;
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->createdDate = (array)$event->element->dateCreated;
                    $this->updatedDate = (array)$event->element->dateUpdated;
                    $this->ElementType = 'Category';
                    $this->ElementTypeId='4';
                    break;




                default:


            }


            return;


        }


        /*
         * returns bool
         * Checks whether generated events exist on the database
         * if it exist then send notification to user
         * if not then.....
         *
         */
        public function SearchRequest($Array):bool
        {

            if(in_array($this->ElementTypeId,array_column($Array,'Notification_type')))
            {
                return true;


            }
                return false;
        }







    }