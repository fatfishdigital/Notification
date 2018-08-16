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
    use fatfish\notification\Notification;
    use fatfish\notification\services\SendNotificationMessageService;
    use verbb\navigation\services\Elements;


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





        public function __construct()
        {
        }

        /**
         * @param $event
         */
        public function actionOnSaveElementEvent($event)
        {
            $data='';
             $ElementType=str_replace('craft\elements\\','',Craft::$app->getElements()->getElementTypeById($event->element->id));

             $this->ParseElement($event);

             if($this->createdDate['date']===$this->updatedDate['date'])
             {
                 $data = [
                     'text'=>$ElementType. ' has been Created By '.$this->UserName. ' [ Title: '.$this->title.' Created Date: '.$this->createdDate['date'].']'
                 ];
             }
             else
             {
                 $data = [
                     'text'=>$ElementType. ' has been updated By '.$this->UserName. ' [ `Title: ` `'.$this->title.'` Updated Date: '.$this->createdDate['date'].']'
                 ];
             }

            SendNotificationMessageService::sendSlackMessage(json_encode($data),"https://hooks.slack.com/services/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");



        }
        public function actionOnDeleteElements($event)
        {


           $this->ParseElement($event);



        }
        public function actionOnResponse($code,$message)
        {
                $data = [
                    'text'=>'` '.$code.' '.$message.'` '
                ];
            SendNotificationMessageService::sendSlackMessage(json_encode($data),"https://hooks.slack.com/services/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");

        }
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
                   break;

                case $event->element instanceof \craft\elements\Asset:
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->title = $event->element->title;
                    $this->fileSize = $event->element->size;
                    $this->fileType = $event->element->kind;
                    $this->ElementType = 'Asset';
                    break;

                case $event->element instanceof \craft\elements\User:

                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->title = $event->element->username;
                    $this->firstname = $event->element->firstname;
                    $this->lastname = $event->element->lastname;
                    $this->email    = $event->element->email;
                    $this->ElementType = 'User';
                    break;

                case $event->element instanceof \craft\elements\Category:
                    $this->title = $event->element->title;
                    $this->UserName = Craft::$app->getUser()->identity->username;
                    $this->createdDate = (array)$event->element->dateCreated;
                    $this->updatedDate = (array)$event->element->dateUpdated;
                    $this->ElementType = 'Category';
                    break;




                default:


            }


return;


        }







    }