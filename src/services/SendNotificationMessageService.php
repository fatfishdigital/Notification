<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 13/8/18
     * Time: 4:05 PM
     */

    namespace fatfish\notification\services;
    use craft\base\Component;
    use craft\mail\Mailer;
    use craft\mail\Message;
    use \GuzzleHttp\Client;
    use Craft;
    class SendNotificationMessageService extends Component
    {
       public static function sendSlackMessage($message,$slackChannel)
        {


            try {

                $client = new Client();
                $res = $client->request('POST',$slackChannel,[
                       'json' => ['blocks' =>$message

                       ]
                ]);


            }
            catch (\Exception $exception)
            {

              Craft::error($exception->getMessage());

            }
            return;
        }
        public static function sendEmail($messge,$mail)
        {
            if(is_null($mail) || empty($mail))
            {
                return false;
            }

            $EmailAddress = explode(',',$mail);
            if(!is_array($EmailAddress))
            {
                $EmailAddress= $mail;
            }
            try {

                $EmailSettings = Craft::$app->getSystemSettings()->getEmailSettings();
                Craft::$app->mailer->compose()
                    ->setFrom($EmailSettings->fromEmail)
                    ->setTo($EmailAddress[0])
                    ->setBcc($EmailAddress)
                    ->setSubject($messge['text'])
                    ->setTextBody($messge['text'])
                    ->setHtmlBody($messge['text'])
                    ->send();
            }
            catch (\Exception $exception)
            {
                Craft::error($exception->getMessage());
            }
        }
    }
