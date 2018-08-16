<?php
    /**
     * Created by PhpStorm.
     * User: fatfish
     * Date: 13/8/18
     * Time: 4:05 PM
     */

    namespace fatfish\notification\services;
    use craft\base\Component;

    class SendNotificationMessageService extends Component
    {



        public static function sendSlackMessage($message,$slackChannel)
        {
            try {


                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $slackChannel);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
                curl_exec($curl);
                curl_close($curl);
                return true;
            }
            catch (\Exception $exception)
            {
                \Craft::$app->session->setNotice($exception->getMessage());
            }





        }






    }