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
    class SendNotificationMessageService extends Component
    {



        public static function sendSlackMessage($message,$slackChannel)
        {
            try {


                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $slackChannel);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
                curl_exec($curl);
                curl_close($curl);
            }
            catch (\Exception $exception)
            {
                \Craft::$app->session->setNotice($exception->getMessage());
            }





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

                $EmailSettings = \Craft::$app->getSystemSettings()->getEmailSettings();
                \Craft::$app->mailer->compose()
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
                throwException("Cannot send Email");
            }
        }






    }