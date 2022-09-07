<?php
namespace app\modules\api\actions\push;

use frontend\modules\api\models\PushMessageForm;
use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * Class MessageAction
 * @package app\modules\api\actions\push
 *
 * [23.11.2015, 14:09:27] [softwebo] Marcin Kawecki: dev serwer: 'ssl://gateway.sandbox.push.apple.com:2195'
[23.11.2015, 14:09:31] [softwebo] Marcin Kawecki: prod: 'ssl://gateway.push.apple.com:2195'
 */

class MessageAction extends Action
{
    public function init()
    {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function run()
    {
        $model = new PushMessageForm();
        $model->setAttributes(Yii::$app->request->post());
        if($model->validate())
        {
            $message = $model->send();
            $value = ['message'=>$message];
        }
        else
        {
            return $model->errors;
        }

        return $value;

    }
}