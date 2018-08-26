
<?php
$getcurrent = dirname(dirname( dirname(__FILE__)));
$systemxml = $getcurrent."/src/cron/system.xml";
        $xml = simplexml_load_file($systemxml);
    $SystemSlack = $xml->Server->Slack;
    $SystemEmail = $xml->Server->Email;
    $ServerXml = $getcurrent."/src/cron/server.xml";

    $serverXml  = simplexml_load_file($ServerXml);
    foreach($serverXml as $server) {
        $ip=gethostbyname('localhost');
         $fp = @fsockopen($ip, (int)$server->port, $err, $errstr);
        if (!$fp) {
            $data = ['text' => $server->name.' Server is Running on Port ' . $server->port . ' is Offline'];
            send_slack_message($data, $server->port,$SystemSlack);
            send_email_message($data, $SystemEmail);
        }
    }


    /**
     * @param $message
     * @param $port
     * @param $slackurl
     */
    function send_slack_message($message,$port,$slackurl)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $slackurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($message),
            CURLOPT_HTTPHEADER => [
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "Postman-Token: 04600ed5-6001-423a-8f57-c8ba69a48e81"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
        return ;
    }

    /**
     * @param $message
     * @param $email
     */
    function send_email_message($message,$email)
    {

        mail($email,'Server Down !!!',$message['text']);
        return ;
    }