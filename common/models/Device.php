<?php

namespace common\models;

use \common\models\base\Device as BaseDevice;
use yii\base\Exception;

/**
 * This is the model class for table "device".
 */
class Device extends BaseDevice
{

	public function sendPush($subject, $text, $type, $id=null, $user_from = null) {
		$params['to'] = $this->token;
		$params['data'] = [
			'title' => $subject,
			'body' => $text,
			'type' => $type,
			'time' => time(),
			'id'=>$id,
			'user_id'=>$user_from
		];
		/*
		$params['notification'] = [
			'title' => $subject,
			'body' => $text,
			'type' => $type,
			'time' => time(),
			'click_action' => $type,
			'sound' => 'default'
		];
	*/
	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: key=AAAAQpY20a0:APA91bHIIffKv7zNyoCQHMpTVmqqC7yhO8D62fehBHsiX44TvtMLhTSxWhxBQRYRXZBlghkHTHGKNNxbiNgFJGCdC9T30qUcW--n6JvFPTorohA8ifAsC8tXAdcTB38Z6f57n6ZTxd9Y'
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_UNESCAPED_UNICODE));

		$answer = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new Exception('Failed call: ' . curl_error($ch) . ' ' . curl_errno($ch));
		}
		curl_close($ch);

		return $answer;

	}

}
