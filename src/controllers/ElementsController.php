<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 13/8/18
     * Time: 12:11 PM
     */

    namespace fatfish\notification\controllers;

    use Composer\Command\ValidateCommand;
    use craft\helpers\ElementHelper;
    use craft\web\Controller;
    use craft\elements\Entry;
    use Craft;
    use DateTime;
    use fatfish\notification\models\CraftNotificationModel;
    use fatfish\notification\records\NotificationRecord;
    use fatfish\notification\records\NotificationSettingRecord;
    use fatfish\notification\services\CraftNotificationService;
    use fatfish\notification\services\SendNotificationMessageService;
    use yii\base\Event;

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
        public $ElementUrl;
        public $ElementEditUrl;
        public $ElementIsNew=false;




        public function __construct()
        {


            $this->AllElementType = NotificationRecord::find()->select('Notification_type')->all();
            $NotificationSetting = Craft::$app->session->get('setting');
            $this->CraftSlackUrl = $NotificationSetting[0]['craftslack'];
            $this->CraftEmail = $NotificationSetting[0]['craftemail'];
          if(empty($this->CraftEmail) || empty($this->CraftSlackUrl))
          {
              $NotificationSettingsRecord = NotificationSettingRecord::find()->all();
              $this->CraftSlackUrl = $NotificationSettingsRecord[0]['craftslack'];
              $this->CraftEmail = $NotificationSettingsRecord[0]['craftemail'];
          }

            }

        /**
         * @param $event
         */
        public function actionOnSaveElementEvent($event)
        {

            $result= $this->ParseElement($event);
            $notification_record_object=$this->SearchRequest($result->ElementType,$event);
            if(Craft::$app->request->getParam('fresh'))
            {
                        if ($this->ElementIsNew && $notification_record_object->Notification_create) {
                            $this->NotifyOnCreate();
                        }
            }
            else
            {
                if(!$this->ElementIsNew && $notification_record_object->Notification_update) {
                    $this->NotifyOnUpdate();
                }
            }
        }


        /**
         * @param $event
         */
        public function actionOnDeleteElements($event)
        {

            $date = explode(" ",$this->createdDate['date']);
            $this->ParseElement($event);
            $notification_record_object='';
            $notification_record_object=$this->SearchRequest( $this->ElementType,$event);
                if (is_object($notification_record_object) && $notification_record_object->Notification_delete) {
                $this->NotifyOnDelete();
            }
        }

        /**
         * @param $code
         * @param $message
         */
        public function actionOnResponse($code,$message,$event)
        {

            $notification_record_object=$this->SearchRequest( $this->ElementType,$event);
            if(is_object($notification_record_object) && $notification_record_object->Notification_exception) {
                $this->NotifyOnException($code,$message);

            }

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
                case $event->element instanceof craft\elements\Entry:
                    $entryurl=Craft::$app->getEntries()->getEntryById($event->element->id);
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->createdDate = (array)$event->element->dateCreated;
                    $this->updatedDate = (array)$event->element->dateUpdated;
                    $this->title=        $event->element->title;
                    $this->ElementTitle = $event->element->title;
                    $this->ElementType = 'Entry';
                    $this->ElementTypeId='1';
                    if(!is_null($entryurl)) {
                        $this->ElementUrl = Craft::$app->getSites()->getCurrentSite()->baseUrl . $entryurl->uri;
                        $this->ElementEditUrl = Craft::$app->getSites()->getCurrentSite()->baseUrl . 'admin/entries/' . $entryurl->uri;
                    }
                    break;

                case $event->element instanceof craft\elements\Asset:
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->ElementTitle = $event->element->title;
                    $this->fileSize = $event->element->size;
                    $this->fileType = $event->element->kind;
                    $this->ElementType = 'Asset';
                    $this->ElementTypeId='2';
                    $this->ElementEditUrl = Craft::$app->getSites()->getCurrentSite()->baseUrl.'admin/assets';
                    $this->ElementUrl = Craft::$app->getSites()->getCurrentSite()->baseUrl.'admin/assets';
                    break;

                case $event->element instanceof craft\elements\User:

                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->title = $event->element->username;
                    $this->ElementTitle = $event->element->firstName;
                    $this->firstname = $event->element->firstName;
                    $this->lastname = $event->element->lastName;
                    $this->email    = $event->element->email;
                    $this->ElementType = 'User';
                    $this->ElementTypeId='4';
                    $this->ElementUrl=Craft::$app->getSites()->getCurrentSite()->baseUrl.'admin/users/'.$event->element->id;
                    $this->ElementEditUrl=Craft::$app->getSites()->getCurrentSite()->baseUrl.'admin/users/'.$event->element->id;

                    break;

                case $event->element instanceof \craft\elements\Category:
                    $this->ElementTitle = $event->element->title;
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->createdDate = (array)$event->element->dateCreated;
                    $this->updatedDate = (array)$event->element->dateUpdated;
                    $this->ElementType = 'Category';
                    $this->ElementTypeId='3';
                    $this->ElementUrl = Craft::$app->getCategories()->getCategoryById($event->element->id)->cpEditUrl;
                    $this->ElementEditUrl = Craft::$app->getCategories()->getCategoryById($event->element->id)->cpEditUrl;
                    break;




                default:


            }


            return $this;


        }


        /*
         * returns object
         * Checks whether generated events exist on the database
         * if it exist then send notification to user
         * if not then.....
         *
         */
        public function SearchRequest($handlename,$element)
        {
            $CraftNotification= new NotificationRecord();
            $CraftNotificationRecord=null;
            $hname='';
            if($handlename==="Entry")
            {
               $hname = Craft::$app->getSections()->getSectionById($element->element->sectionId)->handle;
                $CraftNotificationRecord = $CraftNotification::findOne(['Notification_section_list'=>$hname]);
                     return $CraftNotificationRecord;
             }
            else {
                $CraftNotificationRecord = $CraftNotification::findOne(['Notification_section_list' => $handlename]);

            }
               return $CraftNotificationRecord;

        }



       public function NotifyOnCreate()
       {
           $date = explode(" ",$this->createdDate['date']);

           $data =[
                   [
                           "type"=> "section",
                           "text"=>[
                                   "type"=> "mrkdwn",
                                   "text"=> $this->ElementType." *".$this->ElementTitle." Created by user on $date[0]*, Click <".$this->ElementUrl."|view the Entry>"
                           ],
                   ]
           ];
           SendNotificationMessageService::sendSlackMessage(json_encode($data), $this->CraftSlackUrl);
           SendNotificationMessageService::sendEmail($data,$this->CraftEmail);
       }

       public function NotifyOnUpdate()
       {
           $date = explode(" ",$this->createdDate['date']);
           $data =[
                   [
                           "type"=> "section",
                           "text"=>[
                                   "type"=> "mrkdwn",
                                   "text"=> $this->ElementType." *".$this->ElementTitle." Updated by user on $date[0]*, Click <".$this->ElementUrl."|view the Entry>"
                           ],
                   ]
           ];
           SendNotificationMessageService::sendSlackMessage(json_encode($data), $this->CraftSlackUrl);
           SendNotificationMessageService::sendEmail($data,$this->CraftEmail);
       }
       public function NotifyOnDelete()
       {
           $data =[
                   [
                           "type"=> "section",
                           "text"=>[
                                   "type"=> "mrkdwn",
                                   "text"=> $this->ElementType.': '.$this->ElementTitle.' Deleted by: '.$this->UserName
                           ],
                   ]
           ];
           SendNotificationMessageService::sendSlackMessage(json_encode($data), $this->CraftSlackUrl);
           SendNotificationMessageService::sendEmail($data,$this->CraftEmail);
       }

       public function NotifyOnException($code,$message)
       {
           $data =[
                   [
                           "type"=> "section",
                           "text"=>[
                                   "type"=> "mrkdwn",
                                   "text"=> '` '.$code.' '.$message.'` ',
                           ],
                   ]
           ];
           SendNotificationMessageService::sendSlackMessage(json_encode($data),$this->CraftSlackUrl);
           SendNotificationMessageService::sendEmail($data,$this->CraftEmail);
       }


    }
