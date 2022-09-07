<?php

namespace frontend\modules\api\models;

use yii\base\Model;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class PushMessageForm extends Model
{
    public $message;
    public $deviceToken;
    public $type = 1;

    const PASSPHRASE = 'q1w2e3r4';

    public function rules()
    {
        $rules = [
            [['message', 'deviceToken'], 'required'],
            [['type'], 'in', 'range'=>[0,1]],
        ];

        return $rules;
    }

    public function send()
    {
        // Put your device token here (without spaces):
        $deviceToken = $this->deviceToken;

// Put your private key's passphrase here:
        $passphrase = self::PASSPHRASE;

// Put your alert message here:
        $message = $this->message;

        $serverAddress = $this->getServerAddress();
////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->getCertName());
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
        $fp = stream_socket_client(
            $serverAddress, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        Yii::info('Connected to APNS', 'api');

// Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );

// Encode the payload as JSON
        $payload = json_encode($body);

// Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
        {
            Yii::error([
                'result'=>$result,
                'deviceToken'=>$this->deviceToken,
                'message'=>$this->message,
                'serverAddress'=>$serverAddress,
                ],
                'api\push\message');
            return 'Message not delivered';

        }
        else
        {
            Yii::info([
                'result'=>$result,
                'deviceToken'=>$this->deviceToken,
                'message'=>$this->message,
                'serverAddress'=>$serverAddress
            ], 'api\push\message');
            return 'Message successfully delivered';
        }

// Close the connection to the server
        fclose($fp);

    }

    public function getCertName()
    {
        $list = [
            0 => 'dev.pem',
            1 => 'prod.pem',
        ];
        $filename = $list[$this->type];

        $filePath = Yii::getAlias('@frontend/modules/api/actions/push/').$filename;
//        var_dump($filePath, file_exists($filePath)); die;
        return $filePath;

    }

    public function getServerAddress()
    {
        $address = '';

        $list = [
            0=>'ssl://gateway.sandbox.push.apple.com:2195',
            1=>'ssl://gateway.push.apple.com:2195'
        ];
        if (isset($list[$this->type])==false)
        {
            throw new NotFoundHttpException('Nie znaleziono serwera');
        }
        else
        {
            $address = $list[$this->type];
        }

        return $address;
    }
}